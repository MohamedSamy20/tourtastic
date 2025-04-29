<?php
namespace Modules\Booking\Gateways;

use App\Currency;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\Booking\Events\BookingCreatedEvent;
use Modules\Booking\Events\BookingUpdatedEvent;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Payment;
use Omnipay\Omnipay;
use Omnipay\PayPal\ExpressGateway;
use Illuminate\Support\Facades\Log;

class EcashCheckout extends BaseGateway
{
    public $name = 'Ecash Checkout';
    public $gatewayURL = "https://checkout.ecash-pay.com/";
    /**
     * @var $gateway ExpressGateway
     */
    protected $gateway;
    

    public function getOptionsConfigs()
    {
        return [
            [
                'type'  => 'checkbox',
                'id'    => 'enable',
                'label' => __('Enable Ecash Checkout ?').' '.$this->checkoutType
            ],
            [
                'type'  => 'input',
                'id'    => 'name',
                'label' => __('Custom Name'),
                'std'   => __("Stripe"),
                'multi_lang' => "1"
            ],
            [
                'type'  => 'editor',
                'id'    => 'html',
                'label' => __('Custom HTML Description'),
                'multi_lang' => "1"
            ],
            [
                'type'       => 'input',
                'id'        => 'Ecash_Terminalkey',
                'label'     => __('Terminal Key'),
            ],
            [
                'type'       => 'input',
                'id'        => 'Ecash_MerchantId',
                'label'     => __('Merchant Id'),
            ],
            [
                'type'       => 'input',
                'id'        => 'Ecash_MerchantSecret',
                'label'     => __('Merchant Secret'),
            ],
            [
                'type'       => 'input',
                'id'        => 'endpoint_secret',
                'label'     => __('Webhook Secret'),
                'desc'     => __('Webhook url: <code>:code</code>',['code'=>$this->getWebhookUrl()]),
            ]
        ];
    }


    public function process(Request $request, $booking, $service)
    {
     
           
        if (in_array($booking->status, [
            $booking::PAID,
            $booking::COMPLETED,
            $booking::CANCELLED
        ])) {

            throw new Exception(__("Booking status does need to be paid"));
        }
        if (!$booking->pay_now) {
            throw new Exception(__("Booking total is zero. Can not process payment gateway!"));
        }
        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->payment_gateway = $this->id;
        $payment->amount = (float) $booking->pay_now;
        $payment->save();
        $orderRef = $booking->code.'-'.$payment->id ;
        
        $booking->status = $booking::UNPAID;
        $booking->payment_id = $payment->id;
        $booking->save();
        
        $payment->addMeta('order_ref',$orderRef);
        $booking->addMeta('order_ref',$orderRef);
        
        // {ECashPaymentGatewayURL}/Checkout/{CheckoutType}/{TerminalKey}/{MerchantId}/{VerificationCode}/{Currency}/{Amount}/{Lang}/{OrderRef}/{RedirectUrl}/{CallBackUrl}
        $TerminalKey = $this->getOption('Ecash_Terminalkey'); // 1AN4MA
        $MerchantId = $this->getOption('Ecash_MerchantId'); //WMBXLZ
        $MerchantSecret  = $this->getOption('Ecash_MerchantSecret'); // HGGTB9NNPTK53W2TP4EXTYPI1GZJ7EJ5YS9PA0YJ5S2WV9B1VG649Q185E07RM5M
        // Compute MD5 Hash for (without parentheses)
        //{MerchantId}{MerchantSecret}{Amount}{OrderRef} 
        //Note: Verification Code must be all Capital Letters.


        // $TerminalKey = "1AN4MA";
        // $MerchantId = "WMBXLZ";
        // $MerchantSecret  = "HGGTB9NNPTK53W2TP4EXTYPI1GZJ7EJ5YS9PA0YJ5S2WV9B1VG649Q185E07RM5M";
        
        $Currency = "SYP"; 
        $lng = "AR";
        $checkoutType = $this->checkoutType;
        //$payment->amount =  getAmount(['amount' => $payment->amount ]);
        $gatewayURL =  "https://checkout.ecash-pay.com/";
        $amount =  $payment->amount;
        $VerificationCode = strtoupper(
        md5( $MerchantId.$MerchantSecret. $amount. $orderRef )
        );

        $url = urlencode($this->getReturnUrl() . '?c=' . $booking->code."&p=".$payment->id);
        $url = $this->gatewayURL ."Checkout/".$checkoutType."/".$TerminalKey."/".$MerchantId."/".$VerificationCode."/".$Currency."/".$payment->amount."/".$lng."/".$orderRef
        ."/".$url."/".$url;
           

        return response()->json(['url'=> $url ?? $booking->getDetailUrl()])->send();
        

        throw new Exception('Ecashe Gateway: ' . $response->getMessage());
    }

    public function confirmPayment(Request $request)
    {
        $TerminalKey = "1AN4MA";
        $MerchantId = "WMBXLZ";
        $MerchantSecret  = "HGGTB9NNPTK53W2TP4EXTYPI1GZJ7EJ5YS9PA0YJ5S2WV9B1VG649Q185E07RM5M";
        
        
        $c = $request->query('c');
        $p = $request->query('p');
        
        $booking = Booking::where('code', $c)->first();
        if(!$booking) return abort(404);
        
        if(!request()->TransactionNo){
            $payment = $booking->payment;
            if($payment->status == 'completed')
                    return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
            else
                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));    
        }
        
        Log::info( "process confirmPayment now ".date('Y-m-d H:m'));
        Log::info( request()->all());
        Log::info( "end Request");
        


        $payment = Payment::where('id', $p)->first();
            
        if (!empty($booking) and in_array($booking->status, [$booking::UNPAID]) && $payment) {
            $orderRef = $booking->code.'-'.$payment->id;
            $authToken = request()->Token;
            $TransactionNo = request()->TransactionNo;


            $token = md5( $MerchantId.$MerchantSecret.$TransactionNo.$payment->amount.$orderRef );
            //{MerchantId}{MerchantSecret}{TransactionNo}{Amount}{OrderRef}
            
            // veify auth
            if (request()->IsSuccess == true && strtoupper($token) == $authToken ) {
                $payment = $booking->payment;
                if($payment->status == 'completed')
                    redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
                if ($payment) {
                    $payment->status = 'completed';
                    $payment->logs = \GuzzleHttp\json_encode(request()->all());
                    $payment->save();
                }
                try{
                    
                    $booking->paid += (float)$booking->pay_now;
                    //$booking->pay_now = (float)($oldPaynow - $data['originalAmount'] < 0 ? 0 : $oldPaynow - $data['originalAmount']);
                    $booking->markAsPaid();

                } catch(\Swift_TransportException $e){
                    Log::warning($e->getMessage());
                }
                return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
            } else {

                $payment = $booking->payment;
                if ($payment) {
                    $payment->status = Booking::CANCELLED;
                    $payment->logs = \GuzzleHttp\json_encode(request()->all());
                    $payment->save();
                }
                try{
                    $booking->markAsPaymentFailed();

                } catch(\Swift_TransportException $e){
                    Log::warning($e->getMessage());
                }
                
                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
            }
        }
        
        if (!empty($booking)) {
            return redirect($booking->getDetailUrl(false));
        }elseif (!empty($payment)) {
            if($payment->status == 'completed')
                return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
            else
                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
        } 
        else {
            return redirect(url('/'));
        }
    }

    public function confirmNormalPayment()
    {
        
        Log::info( "process confirmPayment now ".date('Y-m-d H:m'));
        Log::info( request()->all());
        Log::info( "end Request");
        
        /**
         * @var Payment $payment
         */
        $request = \request();
        $c = $request->query('pid');
        $payment = Payment::where('code', $c)->first();

        if (!empty($payment) and in_array($payment->status,['draft'])) {
            $this->getGateway();
            $data = $this->handlePurchaseDataNormal([
                'amount'        => (float)$payment->amount,
                'transactionId' => $payment->code . '.' . time()
            ], $payment);
            $response = $this->gateway->completePurchase($data)->send();
            if ($response->isSuccessful()) {
                return $payment->markAsCompleted(\GuzzleHttp\json_encode($response->getData()));

            } else {
                return $payment->markAsFailed(\GuzzleHttp\json_encode($response->getData()));
            }
        }
        if($payment){
            if($payment->status == 'cancel'){
                return [false,__("Your payment has been canceled")];
            }
        }
        return [false];
    }


    public function processNormal($payment)
    {
        $this->getGateway();
        $payment->payment_gateway = $this->id;
        $data = $this->handlePurchaseDataNormal([
            'amount'        => (float)$payment->amount,
            'transactionId' => $payment->code . '.' . time()
        ],  $payment);

        $response = $this->gateway->purchase($data)->send();

        if($response->isSuccessful()){
            return [true];
        }elseif($response->isRedirect()){
            return [true,false,$response->getRedirectUrl()];
        }else{
            return [false,$response->getMessage()];
        }
    }

    public function cancelPayment(Request $request)
    {
        Log::info( "process cancelPayment now ".date('Y-m-d H:m'));
        Log::info( request()->all());
        Log::info( "end Request");
        
        $c = $request->query('c');
        $booking = Booking::where('code', $c)->first();
        if (!empty($booking) and in_array($booking->status, [$booking::UNPAID])) {
            $payment = $booking->payment;
            if ($payment) {
                $payment->status = 'cancel';
                $payment->logs = \GuzzleHttp\json_encode([
                    'customer_cancel' => 1
                ]);
                $payment->save();
            }

            // Refund without check status
            // $booking->tryRefundToWallet(false);

            return redirect($booking->getDetailUrl())->with("error", __("You cancelled the payment"));
        }
        if (!empty($booking)) {
            return redirect($booking->getDetailUrl());
        } else {
            return redirect(url('/'));
        }
    }

    public function getGateway()
    {

        $this->gateway = Omnipay::create('PayPal_Express');
        $this->gateway->setUsername($this->getOption('account'));
        $this->gateway->setPassword($this->getOption('client_id'));
        $this->gateway->setSignature($this->getOption('client_secret'));
        $this->gateway->setTestMode(false);
        if ($this->getOption('test')) {
            $this->gateway->setUsername($this->getOption('test_account'));
            $this->gateway->setPassword($this->getOption('test_client_id'));
            $this->gateway->setSignature($this->getOption('test_client_secret'));
            $this->gateway->setTestMode(true);
        }
    }

    public function handlePurchaseDataNormal($data, &$payment = null)
    {
        $main_currency = setting_item('currency_main');
        $supported = $this->supportedCurrency();
        $convert_to = $this->getOption('convert_to');
        $data['currency'] = $main_currency;
        $data['returnUrl'] = $this->getReturnUrl(true) . '?pid=' . $payment->code;
        $data['cancelUrl'] = $this->getCancelUrl(true) . '?pid=' . $payment->code;
        if (!array_key_exists($main_currency, $supported)) {
            if (!$convert_to) {
                throw new Exception(__("PayPal does not support currency: :name", ['name' => $main_currency]));
            }
            if (!$exchange_rate = $this->getOption('exchange_rate')) {
                throw new Exception(__("Exchange rate to :name must be specific. Please contact site owner", ['name' => $convert_to]));
            }
            if ($payment) {
                $payment->converted_currency = $convert_to;
                $payment->converted_amount = $payment->amount / $exchange_rate;
                $payment->exchange_rate = $exchange_rate;
                $payment->save();
            }
            $data['amount'] = number_format( $payment->amount / $exchange_rate , 2 );
            $data['currency'] = $convert_to;
        }
        return $data;
    }
    
    public function getAmount($data, $booking, &$payment = null)
    {
        $main_currency = setting_item('currency_main');
        $supported = ["SYP"];
        $convert_to = $this->getOption('convert_to');
        
        if (!array_key_exists($main_currency, $supported)) {
            if (!$convert_to) {
                throw new Exception(__("Ecash does not support currency: :name", ['name' => $main_currency]));
            }
            if (!$exchange_rate = $this->getOption('exchange_rate')) {
                throw new Exception(__("Exchange rate to :name must be specific. Please contact site owner", ['name' => $convert_to]));
            }
            if ($payment) {
                $payment->converted_currency = $convert_to;
                $payment->converted_amount = $booking->pay_now / $exchange_rate;
                $payment->exchange_rate = $exchange_rate;
            }
            $data['originalAmount'] = (float)$booking->pay_now;
            $data['amount'] = number_format( (float)$booking->pay_now / $exchange_rate , 2 );
            $data['currency'] = $convert_to;
        }
        return $data;
        
    }
    public function handlePurchaseData($data, $booking, &$payment = null)
    {
        $main_currency = setting_item('currency_main');
        $supported = $this->supportedCurrency();
        $convert_to = $this->getOption('convert_to');
        $data['currency'] = $main_currency;
        $data['returnUrl'] = $this->getReturnUrl() . '?c=' . $booking->code;
        $data['cancelUrl'] = $this->getCancelUrl() . '?c=' . $booking->code;
        if (!array_key_exists($main_currency, $supported)) {
            if (!$convert_to) {
                throw new Exception(__("PayPal does not support currency: :name", ['name' => $main_currency]));
            }
            if (!$exchange_rate = $this->getOption('exchange_rate')) {
                throw new Exception(__("Exchange rate to :name must be specific. Please contact site owner", ['name' => $convert_to]));
            }
            if ($payment) {
                $payment->converted_currency = $convert_to;
                $payment->converted_amount = $booking->pay_now / $exchange_rate;
                $payment->exchange_rate = $exchange_rate;
            }
            $data['originalAmount'] = (float)$booking->pay_now;
            $data['amount'] = number_format( (float)$booking->pay_now / $exchange_rate , 2 );
            $data['currency'] = $convert_to;
        }
        return $data;
    }

    public function supportedCurrency()
    {
        return [
            "syp" => "syp",
        ];
    }
}

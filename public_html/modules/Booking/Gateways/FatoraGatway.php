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

use Illuminate\Support\Facades\Http;


use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FatoraGatway extends BaseGateway
{
    public $name = 'Fatora Checkout';
    public $gatewayURL = "https://egate-t.fatora.me/";
    
    // api/create-payment
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
                'label' => __('Enable Fatora Checkout ?')
            ],
            [
                'type'  => 'input',
                'id'    => 'name',
                'label' => __('Custom Name'),
                'std'   => __("Fatora"),
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
                'id'        => 'terminalId',
                'label'     => __('Terminal Id'),
            ],
            [
                'type'       => 'input',
                'id'        => 'appUser',
                'label'     => __('app User'),
            ],
            [
                'type'       => 'input',
                'id'        => 'username',
                'label'     => __('username'),
            ],
              [
                'type'       => 'input',
                'id'        => 'password',
                'label'     => __('password'),
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
        $payment->currency = "SYP";
        $payment->payment_gateway = $this->id;
        $payment->amount = (float) $booking->pay_now;
        $payment->save();
        $orderRef = $booking->code.'-'.$payment->id ;
        
        $booking->status = $booking::UNPAID;
        $booking->payment_id = $payment->id;
        $booking->save();
        
        $payment->addMeta('order_ref',$orderRef);
        $booking->addMeta('order_ref',$orderRef);
        
        //terminalId,appUser,username,password
        $TerminalId =  $this->getOption('terminalId'); // 1AN4MA
        $appUser = $this->getOption('appUser'); //WMBXLZ
        $username  = $this->getOption('username');
        $password  = $this->getOption('password'); // 
        // $Currency = "SYP"; 
        $lng = app()->getlocale();
        //$payment->amount =  getAmount(['amount' => $payment->amount ]);
        $gatewayURL =  $this->gatewayURL.'api/create-payment';
        $amount =  $payment->amount;
        $url = $this->getReturnUrl() . '?c=' . $booking->code."&p=".$payment->id;

        try{
     
     
            $response = Http::withBasicAuth($username,$password)
            ->accept('application/json')
            ->post($gatewayURL,[
            "lang" => $lng,
            "terminalId" => $TerminalId,
            "callbackURL" => $url,
            "triggerURL" => $url."&check=true",
            "amount" => $amount,
            ])->json();
        
            if($response['ErrorMessage'] == "Success" && $response['ErrorCode'] == 0 && isset($response['Data']['url'])){
                $paymentId = $response['Data']['paymentId'];
                $url = $response['Data']['url'];
                $payment->addMeta('paymentId',$paymentId);
                $payment->save();
                return response()->json(['url'=> $url ?? $booking->getDetailUrl()])->send();
            }
        } catch(\Swift_TransportException $e){
            return '<div class="alert alert-warning" role="alert">Error please try again!</div>';
        }
        
        throw new Exception('Ecashe Gateway: ' . $response->getMessage());
    }

    public function confirmPayment(Request $request)
    {
        $TerminalId =  $this->getOption('terminalId');
        $appUser = $this->getOption('appUser'); 
        $username  = $this->getOption('username');
        $password  = $this->getOption('password');
        $gatewayURL =  $this->gatewayURL.'api/create-payment';

        
        $c = $request->query('c');
        $p = $request->query('p');
        
        
        
        Log::info( "process confirmPayment now ".date('Y-m-d H:m'));
        Log::info( request()->all());
        Log::info( request()->fullUrl());
        
        $booking = Booking::where('code', $c)->first();
        if(!$booking) return abort(404);
        
        $payment = $booking->payment;
        // dd($payment);
        if(
            !$payment->status &&
            $payment->getMeta('paymentId') 
            && request()->check 
            && in_array($booking->status, [$booking::UNPAID] )
            
            )
        {
            
            $paymentId = $payment->getMeta('paymentId');
            
            try{
                // $payment = $booking->payment;
                $response = Http::withBasicAuth($username,$password)
                ->accept('application/json')
                ->get( $this->gatewayURL."api/get-payment-status/".$paymentId)->json();
                // dd( 
                //     $response , $payment,
                //     $response['ErrorMessage'] == "Success" &&  isset($response['Data']['status']),
                //     $response['Data']['status'] == 'A' 
                // );
                if($response['ErrorMessage'] == "Success" &&  isset($response['Data']['status'])){
                    // $payment = $booking->payment;
                    if($response['Data']['status'] == 'A' && $payment->amount == $response['Data']['amount']){
                        $payment->status = 'completed';
                        $booking->paid += (float)$booking->pay_now;
                        $booking->markAsPaid();
                        $payment->logs = $response;
                        $payment->save();
                        $booking->save();
                        return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
                    }else{
                        $payment->status = Booking::CANCELLED;
                        $payment->logs = $response;
                        $payment->save();
                        $booking->save();
                        return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
                    }
                            
                }
                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
            } catch(\Exception $e){
                // Log::info( $e);
                // Log::info( request()->fullUrl());
                dd($e);
                return redirect($booking->getDetailUrl())->with("warning", __("Error please try again"));
            }
        }

        return redirect($booking->getDetailUrl(false));
                
                
        

    
        // if (!empty($booking) and in_array($booking->status, [$booking::UNPAID]) && $payment) {
            
         
    }


    public function processNormal($payment)
    {
        return $this->process($payment);
       
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

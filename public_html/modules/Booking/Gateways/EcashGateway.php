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

class EcashGateway extends EcashCheckout
{
    public $name = 'Ecash Checkout Card';
    // public $gatewayURL = "https://checkout.ecash-pay.co/";
    public $checkoutType = "Card";
    /**
     * @var $gateway ExpressGateway
     */
    protected $gateway;
    

    
}

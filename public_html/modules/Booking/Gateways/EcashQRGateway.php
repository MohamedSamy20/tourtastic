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

class EcashQRGateway extends EcashCheckout
{
    public $name = 'Ecash Checkout QR';
    public $checkoutType = "QR";
    /**
     * @var $gateway ExpressGateway
     */
    protected $gateway;
    

    
}

<?php
return [
    'gateways'=>[
        'offline_payment'=>Modules\Booking\Gateways\OfflinePaymentGateway::class,
        'paypal'=>Modules\Booking\Gateways\PaypalGateway::class,
        'stripe'=>Modules\Booking\Gateways\StripeGateway::class,
        'payrexx'=>Modules\Booking\Gateways\PayrexxGateway::class,
        
        
        'payrexx'=>Modules\Booking\Gateways\PayrexxGateway::class,
        
        'Ecash' => Modules\Booking\Gateways\EcashGateway::class,
        

        'EcashQR' => Modules\Booking\Gateways\EcashQRGateway::class,
        
        'Fatora' => Modules\Booking\Gateways\FatoraGatway::class
        
    ],
];

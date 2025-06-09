<?php
return [
    'token' => env('MERCADOPAGO_ACCESS_TOKEN', ''),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY', ''),
    'sandbox' => true, // o false si querés producción
];

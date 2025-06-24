<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('tu_clave_secreta');

$productos = [
    'prod_001' => ['nombre' => 'Libro', 'precio' => 20.00, 'iva' => 0.00],
    'prod_002' => ['nombre' => 'Laptop', 'precio' => 1000.00, 'iva' => 0.21],
];

$carrito = $_POST['carrito']; // Ejemplo: ['prod_001' => 2, 'prod_002' => 1]

$line_items = [];

foreach ($carrito as $producto_id => $cantidad) {
    $producto = $productos[$producto_id];
    $base_precio = $producto['precio'];
    $iva = $producto['iva'];
    $precio_con_iva = $base_precio * (1 + $iva);

    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $producto['nombre'],
            ],
            'unit_amount' => intval($precio_con_iva * 100), // en centavos
        ],
        'quantity' => $cantidad,
    ];
}

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'https://tudominio.com/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://tudominio.com/cancel.php',
]);

echo json_encode(['id' => $session->id]);

<?php
// checkout.php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

// 1. YOUR ACTUAL CLEAN SECRET KEY
$stripeSecretKey = 'sk_test_51TCsPTAf41edsuyy0orsGR8kPh48adjwvGjj5YBcIe7HD8IVrgKI758FkdUe4GlEy1k8Dz5tI5bTskUJ1p2Q9Pp200nTAkYgSl';

$client = new Client();

// 2. Safely grab the price ID from your products.php button
if (!isset($_POST['price_id'])) {
    die("<h3>Error: No product selected.</h3><p>Please go back to <a href='products.php'>products.php</a> and click 'Buy Now'.</p>");
}

$priceId = $_POST['price_id'];

try {
    $response = $client->request('POST', 'https://api.stripe.com/v1/checkout/sessions', [
        'auth' => [$stripeSecretKey, ''],
        'form_params' => [
            'success_url' => 'http://localhost/stripe-lab/stripe-php-app/success.php',
            'cancel_url' => 'http://localhost/stripe-lab/stripe-php-app/cancel.php',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            // 3. 'payment' is for one-time purchases (Standard for Lab 8)
            // Note: If you made your products "Recurring/Monthly" in Stripe, change this to 'subscription'
            'mode' => 'payment', 
        ],
    ]);

    $session = json_decode($response->getBody(), true);

    // Redirect the user to Stripe's Hosted Checkout page
    if (isset($session['url'])) {
        header("Location: " . $session['url']);
        exit;
    }

} catch (\GuzzleHttp\Exception\ClientException $e) {
    // This will print the exact Stripe error if something goes wrong
    $errorBody = json_decode($e->getResponse()->getBody(), true);
    echo "<h3>Stripe API Error:</h3>";
    echo "<pre>";
    print_r($errorBody);
    echo "</pre>";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage();
}
<?php
// config.php

// CLEANED KEY: No extra "HERE" text or spaces
$secret_key = 'sk_test_51TCsPTAf41edsuyy0orsGR8kPh48adjwvGjj5YBcIe7HD8IVrgKI758FkdUe4GlEy1k8Dz5tI5bTskUJ1p2Q9Pp200nTAkYgSl';

function stripeRequest($endpoint, $method = 'GET', $data = []) {
    global $secret_key;
    
    // trim() removes accidental spaces
    $clean_key = trim($secret_key);
    
    $ch = curl_init("https://api.stripe.com/v1/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Authentication
    curl_setopt($ch, CURLOPT_USERPWD, $clean_key . ":");
    
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        return ['error' => ['message' => curl_error($ch)]];
    }
    
    curl_close($ch);
    return json_decode($response, true);
}
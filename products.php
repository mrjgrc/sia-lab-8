<?php
// products.php
require 'vendor/autoload.php';

// Hardcoded key so it doesn't need config.php anymore!
$stripeSecretKey = 'sk_test_51TCsPTAf41edsuyy0orsGR8kPh48adjwvGjj5YBcIe7HD8IVrgKI758FkdUe4GlEy1k8Dz5tI5bTskUJ1p2Q9Pp200nTAkYgSl';

$client = new \GuzzleHttp\Client();

try {
    $response = $client->request('GET', 'https://api.stripe.com/v1/products', [
        'headers' => ['Authorization' => 'Bearer ' . $stripeSecretKey],
        'query'   => ['active' => 'true', 'limit' => 10]
    ]);
    
    $productsData = json_decode($response->getBody(), true);
    $products = $productsData['data'];
    
} catch (\Exception $e) {
    die("<h3>Error:</h3> " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Product Catalog</h1>
    
    <div class="product-grid">
        <?php foreach ($products as $item): 
            try {
                $pRes = $client->request('GET', 'https://api.stripe.com/v1/prices', [
                    'headers' => ['Authorization' => 'Bearer ' . $stripeSecretKey],
                    'query' => ['product' => $item['id']]
                ]);
                $pData = json_decode($pRes->getBody(), true);
                if (empty($pData['data'])) continue;
                $price = $pData['data'][0];
            } catch (\Exception $e) { continue; }
        ?>
            <div class="product-card">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <div class="price">
                    <?= strtoupper($price['currency']) ?> <?= number_format($price['unit_amount'] / 100, 2) ?>
                </div>
                <form action="checkout.php" method="POST">
                    <input type="hidden" name="price_id" value="<?= $price['id'] ?>">
                    <button type="submit" class="buy-btn">Buy Now</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
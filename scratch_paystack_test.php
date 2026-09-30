<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Paystack configuration...\n";
$secretKey = config('services.paystack.secret_key');
$publicKey = config('services.paystack.public_key');
$baseUrl = config('services.paystack.base_url');

echo "Secret Key: " . substr($secretKey, 0, 12) . "...\n";
echo "Public Key: " . substr($publicKey, 0, 12) . "...\n";
echo "Base URL: " . $baseUrl . "\n\n";

$gateway = app(\App\Contracts\PaymentGateway::class);

$testRef = 'TEST-PAYSTACK-' . time();
echo "Initializing Paystack payment with reference {$testRef}...\n";

try {
    $dto = new \App\DataTransferObjects\PaymentInitiationData(
        email: 'test_user@example.com',
        amountInKobo: 100000,
        reference: $testRef,
        currency: 'NGN',
        callbackUrl: 'http://localhost/connections/pay/callback',
        metadata: ['test' => true]
    );

    $initResult = $gateway->initialize($dto);

    echo "--- Paystack Initialization Response ---\n";
    print_r($initResult);

    if (!empty($initResult['authorization_url']) && !empty($initResult['reference'])) {
        echo "\nSUCCESS: Paystack initialized successfully!\n";
        echo "Authorization URL: " . $initResult['authorization_url'] . "\n";
        echo "Access Code: " . ($initResult['access_code'] ?? 'N/A') . "\n";
    } else {
        echo "\nERROR: Paystack returned incomplete initialization data.\n";
    }

    echo "\nTesting Paystack Verification API for reference {$testRef}...\n";
    $verifyResult = $gateway->verify($testRef);

    echo "--- Paystack Verification Response ---\n";
    echo "Successful: " . ($verifyResult->successful ? 'true' : 'false') . "\n";
    echo "Status: " . $verifyResult->status->value . "\n";
    echo "Amount: " . $verifyResult->amountInKobo . " kobo\n";
    echo "Currency: " . $verifyResult->currency . "\n";
    echo "Provider: " . $verifyResult->provider . "\n";

} catch (\Throwable $e) {
    echo "\nEXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

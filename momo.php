<?php
require_once 'functions.php';

/**
 * MoMo Payment Integration
 * 
 * This file handles the integration with MoMo payment gateway for processing payments.
 * It includes configuration, request generation, and response handling.
 */

// MoMo payment configuration
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create"; // MoMo API endpoint
$partnerCode = "MOMO4MUD20240115_TEST"; // Your MoMo partner code
$accessKey = "Ekj9og2VnRfOuIys"; // Your MoMo access key
$secretKey = "PseUbm2s8QVJEbexsh8H3Jz2qa9tDqoa"; // Your MoMo secret key
$orderInfo = "Thanh toán đơn hàng";
$redirectUrl = "http://localhost/shop/checkout.php"; // URL to redirect after payment
$cancelUrl = "http://localhost/shop/checkout.php"; // URL to redirect if payment is cancelled
$ipnUrl = "http://localhost/shop/checkout.php"; // Instant Payment Notification URL
$extraData = "";

// Handle MoMo callback
if (isset($_GET['resultCode'])) {
    $resultCode = $_GET['resultCode'];
    $message = $_GET['message'] ?? '';
    $orderId = $_GET['orderId'] ?? '';
    
    // Log the callback response
    error_log("MoMo Callback - Result Code: " . $resultCode . ", Message: " . $message . ", Order ID: " . $orderId);
    
    // Redirect based on result code
    switch ($resultCode) {
        case '0': // Success
            header('Location: order_success.php?status=success&source=momo&orderId=' . $orderId);
            break;
        case '1006': // User cancelled
            header('Location: order_cancel.php?source=momo&message=' . urlencode('Bạn đã hủy thanh toán'));
            break;
        case '1005': // Payment expired
            header('Location: order_cancel.php?source=momo&message=' . urlencode('Thanh toán đã hết hạn'));
            break;
        default: // Other errors
            header('Location: order_cancel.php?source=momo&message=' . urlencode($message));
            break;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate unique order ID using timestamp
        $orderId = time() . "";
        
        // Get total amount from cart
        $amount = cart_total();
        
        // Create order information
        $orderInfo = "Thanh toán đơn hàng #" . $orderId;
        $requestId = time() . "";
        $requestType = "captureWallet"; // Payment method: capture from MoMo wallet

        /**
         * Create signature for security
         * The signature is created using HMAC SHA256 algorithm
         * It includes all important parameters to prevent tampering
         */
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        /**
         * Prepare request data
         * This data will be sent to MoMo payment gateway
         */
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MOMO",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );

        // Log the request data for debugging
        error_log("MoMo Request Data: " . json_encode($data));

        // Send request to MoMo payment gateway
        $result = execPostRequest($endpoint, json_encode($data));
        
        // Log the response for debugging
        error_log("MoMo Response: " . $result);
        
        $jsonResult = json_decode($result, true);

        // Set proper content type for JSON response
        header('Content-Type: application/json');

        /**
         * Handle the response from MoMo
         * If successful, return the payment URL
         * If failed, return error message
         */
        if (isset($jsonResult['payUrl'])) {
            echo json_encode([
                'success' => true,
                'payUrl' => $jsonResult['payUrl'],
                'orderId' => $orderId
            ]);
            exit;
        } else {
            $errorMessage = $jsonResult['message'] ?? 'Payment processing failed';
            error_log("MoMo Error Response: " . json_encode($jsonResult));
            throw new Exception($errorMessage);
        }
    } catch (Exception $e) {
        error_log("MoMo Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Helper function to send POST request to MoMo
 * 
 * @param string $url The endpoint URL
 * @param string $data The JSON data to send
 * @return string The response from the server
 */
function execPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    // Enable error reporting for debugging
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $result = curl_exec($ch);
    
    // Log any cURL errors
    if ($result === false) {
        $error = curl_error($ch);
        error_log("MoMo cURL Error: " . $error);
        throw new Exception("Connection error: " . $error);
    }
    
    // Log the verbose output for debugging
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    error_log("MoMo cURL Verbose: " . $verboseLog);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        error_log("MoMo HTTP Error: " . $httpCode . " Response: " . $result);
        throw new Exception("HTTP Error: " . $httpCode);
    }
    
    curl_close($ch);
    return $result;
} 
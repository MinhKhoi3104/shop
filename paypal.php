<?php
require_once 'functions.php';

/**
 * PayPal Payment Integration
 * 
 * This file handles the integration with PayPal payment gateway for processing payments.
 * It includes configuration, request generation, and response handling.
 */

// PayPal configuration
$paypalClientId = "AZVw7qrRKACLvctbtFwhF2rLTDur9sWwyhCDEqjNF0nqvzr0yDSTU1UJOU2CuUc87EiAZpURFsW0Y4AQ"; // Replace with your PayPal client ID
$paypalClientSecret = "EGdQvZD3RRKGOyFR4AGgk9C1SUScbAtN0Q1MLD33Mv5oK-5Wh6Vhg3lzmjHRbmyramfg8RmgJK-DSygi"; // Replace with your PayPal client secret
$paypalMode = "sandbox"; // Change to "live" for production
$paypalEndpoint = ($paypalMode === "sandbox") 
    ? "https://api-m.sandbox.paypal.com" 
    : "https://api-m.paypal.com";
$redirectUrl = "http://localhost/shop/order_success.php?status=success&source=paypal"; // URL to redirect after payment
$cancelUrl = "http://localhost/shop/checkout.php?status=cancelled&source=paypal"; // URL to redirect if payment is cancelled

// VND to USD conversion rate (you should use a real-time API in production)
$vndToUsdRate = 0.000041; // 1 VND = 0.000041 USD (approximate)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate unique order ID using timestamp
        $orderId = time() . "";
        
        // Get total amount from cart
        $amountVnd = cart_total();
        
        // Convert VND to USD
        $amountUsd = $amountVnd * $vndToUsdRate;
        
        // Create order information
        $orderInfo = "Order #" . $orderId;

        /**
         * Prepare request data for PayPal
         * This creates a PayPal order with the specified amount
         */
        $data = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => $orderId,
                    'description' => $orderInfo,
                    'amount' => array(
                        'currency_code' => 'USD',
                        'value' => number_format($amountUsd, 2, '.', '')
                    )
                )
            ),
            'application_context' => array(
                'return_url' => $redirectUrl,
                'cancel_url' => $cancelUrl,
                'brand_name' => 'Simple Green',
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING'
            )
        );

        // First, get an access token
        $accessToken = getPayPalAccessToken($paypalClientId, $paypalClientSecret, $paypalEndpoint);
        
        if (!$accessToken) {
            throw new Exception('Failed to get PayPal access token');
        }

        // Create PayPal order
        $createOrderUrl = $paypalEndpoint . "/v2/checkout/orders";
        $result = execPayPalRequest($createOrderUrl, 'POST', json_encode($data), $accessToken);
        $jsonResult = json_decode($result, true);

        // Set proper content type for JSON response
        header('Content-Type: application/json');

        if (isset($jsonResult['id'])) {
            // Find the approve link
            $approveLink = '';
            foreach ($jsonResult['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approveLink = $link['href'];
                    break;
                }
            }
            
            if (empty($approveLink)) {
                throw new Exception('No approval URL found in PayPal response');
            }

            echo json_encode([
                'paypalUrl' => $approveLink,
                'orderId' => $jsonResult['id']
            ]);
            exit;
        } else {
            throw new Exception('Invalid PayPal response: ' . json_encode($jsonResult));
        }
    } catch (Exception $e) {
        error_log("PayPal Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'error' => true, 
            'message' => 'Payment processing failed: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Get PayPal access token
 * 
 * @param string $clientId PayPal client ID
 * @param string $clientSecret PayPal client secret
 * @param string $apiEndpoint PayPal API endpoint
 * @return string|false Access token or false on failure
 */
function getPayPalAccessToken($clientId, $clientSecret, $apiEndpoint) {
    $ch = curl_init($apiEndpoint . "/v1/oauth2/token");
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    
    if ($error) {
        error_log("PayPal cURL Error: " . $error);
        return false;
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        error_log("PayPal Token Error: " . json_encode($data));
        return false;
    }
    
    return $data['access_token'];
}

/**
 * Execute PayPal API request
 * 
 * @param string $url The endpoint URL
 * @param string $method HTTP method
 * @param string $data The JSON data to send
 * @param string $accessToken PayPal access token
 * @return string The response from the server
 */
function execPayPalRequest($url, $method, $data, $accessToken) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    // Enable error reporting for debugging
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $result = curl_exec($ch);
    
    if ($result === false) {
        $error = curl_error($ch);
        error_log("PayPal Request Error: " . $error);
        throw new Exception("cURL Error: " . $error);
    }
    
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    error_log("PayPal Request Details: " . $verboseLog);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
        error_log("PayPal HTTP Error: " . $httpCode . " Response: " . $result);
        throw new Exception("HTTP Error: " . $httpCode);
    }
    
    curl_close($ch);
    return $result;
} 
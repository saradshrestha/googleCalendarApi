<?php
require __DIR__ . '/vendor/autoload.php';
require 'GoogleClientGlobal.php';
session_start();

$googleClientGlobal = new GoogleClientGlobal();
$client = $googleClientGlobal->getClient();

// If there is an authorization code, exchange it for an access token
if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        // Check if there's an error
        if (isset($token['error'])) {
            throw new Exception('Error fetching access token: ' . htmlspecialchars($token['error']));
        }

        // Store the access token in the session
        $_SESSION['access_token'] = $token;

        // If a refresh token is provided, store it in the session
        if (isset($token['refresh_token'])) {
            $_SESSION['refresh_token'] = $token['refresh_token'];
        }
        $_SESSION['message'] = 'Successfully Logged In';
        $_SESSION['status'] = 'success';
        // Redirect to index page after successful token exchange
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        // Handle any exceptions that occur during token exchange
        echo 'Error: ' . htmlspecialchars($e->getMessage());
        error_log('Error fetching access token: ' . $e->getMessage());
        exit();
    }
} else {
    // If there's no authorization code, redirect to get one
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
}
?>

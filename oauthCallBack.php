<?php
require __DIR__ . '/vendor/autoload.php';

session_start();

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');

// If there is an authorization code, exchange it for an access token
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        echo 'Error fetching access token: ' . htmlspecialchars($token['error']);
        exit();
    }

    // Store the access token and refresh token in the session
    $_SESSION['access_token'] = $token;
   
    if (isset($token['refresh_token'])) {
        $_SESSION['refresh_token'] = $token['refresh_token'];
    }

    // Redirect to the main page
    header('Location: index.php');
    exit();
} else {
    // If there's no authorization code, redirect to get one
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
}
?>

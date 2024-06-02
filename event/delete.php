<?php
require __DIR__ . '../../vendor/autoload.php'; // Adjusted the path
require __DIR__ . '../../GoogleClientGlobal.php'; // Adjusted the path

session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['eventId'])) {
    try {
        $googleClientGlobal = new GoogleClientGlobal();
        $client = $googleClientGlobal->getClient();
        $client->setAccessToken($_SESSION['access_token']); // Set the access token

        $service = new Google_Service_Calendar($client);
        $service->events->delete('primary', $_GET['eventId']);
        
        $_SESSION['message'] = 'Event deleted successfully';
    } catch (Google_Service_Exception $e) {
        $errors = $e->getErrors();
        echo 'Error: ' . htmlspecialchars($errors[0]['message']);
        error_log($e->getMessage());
    } catch (Exception $e) {
        echo 'An error occurred: ' . htmlspecialchars($e->getMessage());
        error_log($e->getMessage());
    }
}

header('Location: ../index.php');
exit();
?>

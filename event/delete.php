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
            $_SESSION['status'] = 'success';
        } catch (Google_Service_Exception $e) {
            $errors = $e->getErrors();
            $_SESSION['message'] = htmlspecialchars($errors[0]['message']);
            $_SESSION['status'] = 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
        }
    }

    header('Location: ../index.php');
    exit();


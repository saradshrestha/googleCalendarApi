<?php
require __DIR__ . '../../vendor/autoload.php';

require_once 'GoogleClientGlobal.php';

session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

$googleClientGlobal = new GoogleClientGlobal();

$client = $googleClientGlobal->getClient();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function formatDateTimeToISO8601($datetimeLocal, $timezone = 'America/Los_Angeles')
    {
        $date = new DateTime($datetimeLocal, new DateTimeZone($timezone));
        return $date->format(DateTime::ATOM); // DateTime::ATOM is equivalent to 'Y-m-d\TH:i:sP'
    }
    try {
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $_POST['summary'],
            'location' => $_POST['location'],
            'description' => $_POST['description'],
            'start' => array(
                'dateTime' => formatDateTimeToISO8601($_POST['start'], 'America/Los_Angeles'),
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                'dateTime' => formatDateTimeToISO8601($_POST['end'], 'America/Los_Angeles'),
                'timeZone' => 'America/Los_Angeles',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=2'
            ),
            'attendees' => array(),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                )
            ),
        ));

        $event = $service->events->insert('primary', $event);
        $_SESSION['message'] = 'success';
        header('Location: ../index.php');
    } catch (Google_Service_Exception $e) {
        $errors = $e->getErrors();
        echo 'Error: ' . htmlspecialchars($errors[0]['message']);
        error_log($e->getMessage());
    } catch (Exception $e) {
        echo 'An error occurred: ' . htmlspecialchars($e->getMessage());
        error_log($e->getMessage());
    }
}
include('../includes/header.html');
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="title">Event Title:</label>
                        <input class="form-control" type="text" id="title" name="summary" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" class="form-control" name="location">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="start">Start Time (YYYY-MM-DDTHH:MM:SS):</label>
                        <input class="form-control" type="datetime-local" id="start" name="start" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="end">End Time (YYYY-MM-DDTHH:MM:SS):</label>
                        <input type="datetime-local" class="form-control" id="end" name="end" required>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" class="form-control" name="description"></textarea>
                    </div>
                </div>
                <buttom class="btn btn-primary float-right" type="submit">Create Event</buttom>
            </form>
        </div>
    </div>

    <?php
        include('../includes/footer.html')
    ?>
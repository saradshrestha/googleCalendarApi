<?php
require __DIR__ . '../../vendor/autoload.php'; // Adjusted the path

require_once __DIR__ . '../../GoogleClientGlobal.php'; // Adjusted the path

session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

$googleClientGlobal = new GoogleClientGlobal();
$client = $googleClientGlobal->getClient();
$client->setAccessToken($_SESSION['access_token']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function formatDateTimeToISO8601($datetimeLocal, $timezone = 'America/Los_Angeles') {
        $date = new DateTime($datetimeLocal, new DateTimeZone($timezone));
        return $date->format(DateTime::ATOM); 
    }

    try {
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $_POST['summary'],
            'location' => $_POST['location'],
            'description' => $_POST['description'],
            'start' => array(
                'dateTime' => formatDateTimeToISO8601($_POST['start']),
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                'dateTime' => formatDateTimeToISO8601($_POST['end']),
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
        $_SESSION['message'] = 'Event created successfully';
        $_SESSION['status'] = 'success';
        header('Location: ../index.php');
        exit();
    } catch (Google_Service_Exception $e) {
        $errors = $e->getErrors();
        $_SESSION['message'] = htmlspecialchars($errors[0]['message']);
        $_SESSION['status'] = 'error';
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['status'] = 'error';
    }
}

include('../includes/header.php');
?>
    <div class="card">
        <div class="card-header">
            <h5>Add New Event</h5>
        </div>  
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="title">Event Title:</label>
                        <input class="form-control" type="text" id="title" placeholder="Title..." name="summary" required >
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" class="form-control" placeholder="Location..." name="location">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="start">Start Time:</label>
                        <input class="form-control" type="datetime-local" id="start" name="start" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="end">End Time:</label>
                        <input type="datetime-local" class="form-control" id="end" name="end" required>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" class="form-control" placeholder="Descriptions..." name="description"></textarea>
                    </div>
                </div>
                <button class="btn btn-primary float-right" type="submit">Create Event</button>
            </form>
        </div>
    </div>
</div>

<?php
include('../includes/footer.php');
?>

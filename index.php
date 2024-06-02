<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'GoogleClientGlobal.php';

session_start();

$googleClientGlobal = new GoogleClientGlobal();

$client = $googleClientGlobal->getClient();

$authUrl = null;

try{
    if (!isset($_SESSION['access_token'])) {

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);

            echo $token . 'true';
            if (array_key_exists('refresh_token', $token)) {
                $_SESSION['refresh_token'] = $token['refresh_token'];
            } elseif (isset($_SESSION['refresh_token'])) {
                $client->refreshToken($_SESSION['refresh_token']);
                $token = $client->getAccessToken();
            }

            $_SESSION['access_token'] = $token;
                header('Location: index.php');
               
            exit();
        } else {
            $authUrl = $client->createAuthUrl();
        }
    } else {
        $client->setAccessToken($_SESSION['access_token']);

        // Refresh the token if it's expired
        if ($client->isAccessTokenExpired()) {
            if (isset($_SESSION['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
                $_SESSION['access_token'] = $client->getAccessToken();
               
            } else {
                // Handle the case where there is no refresh token
                unset($_SESSION['access_token']);
               
                header('Location: index.php');
                exit();
            }
        }

        $service = new Google_Service_Calendar($client);

        // Displays event data
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();
        $accessTokenStatus = true;
       
    }
}
catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['status'] = 'error';
}

include('includes/header.php');

?>

    <div class="card">
        <div class="card-header">
            <h4>Upcoming Events</h4>
        </div>
        <div class="card-body">
            <?php
            if($authUrl != null){
                echo "<a class='btn btn-primary' href='" . filter_var($authUrl, FILTER_SANITIZE_URL) . "'>Connect to Google Calendar</a>";
            }elseif (empty($events)) {
                echo "<p>No upcoming events found.</p>";
            } else {
                echo "<ol>";
                foreach ($events as $event) {
                    $start = $event->start->dateTime;
                    if (empty($start)) {
                        $start = $event->start->date;
                    }
                    echo "<li>" . htmlspecialchars($event->getSummary()) . " (" . htmlspecialchars($start) . ") 
                    <a class='btn btn-danger ml-4 my-auto' style='line-height: 1; padding:2px 5px;' href='event/delete.php?eventId=" . htmlspecialchars($event->getId()) . "'>X</a></li>";
                }
                echo "</ol>";
            }
            ?>
        </div>
    </div>
</div>
<?php

include('includes/footer.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar API</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Google Calendar API</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Home</a>
                </li>
                <?php
                if(isset($_SESSION['access_token'])){
                    echo '<li class="nav-item">
                            <a class="nav-link" href="/event/create.php">Create Event</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?logout=true">Logout</a>
                        </li>';
                }

                ?>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/event/create.php">Create Event</a>
                </li> -->
                
            </ul>
        </div>
    </nav>
    <div class="container mt-5"></div>
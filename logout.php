<?php
require __DIR__ . '/vendor/autoload.php';

session_start();
    try{
        unset($_SESSION['access_token']);
        $_SESSION['message'] = 'Successfully Logout';
        $_SESSION['status'] = 'success';
        header('Location: ../index.php');
        exit();
    }
     catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['status'] = 'error';
    }
   




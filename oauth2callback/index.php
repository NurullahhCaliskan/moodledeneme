<?php


require_once('../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once("{$CFG->dirroot}/vendor/autoload.php");


session_start();

$client = new Google_Client();
$client->setClientId('675994815024-rfj7s2bhprn9tcaiup4sc2kavm9f3p84.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/oauth2callback');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');

if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();

    $redirect_uri = new moodle_url('/');
    redirect(new moodle_url('/'));

    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


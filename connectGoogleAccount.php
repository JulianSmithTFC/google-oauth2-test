<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/connectGoogleAccount.php');
$client->addScope('https://www.googleapis.com/auth/plus.business.manage');
$client->setAccessType('offline');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/getGoogleAccount.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

} else {

    if (! isset($_GET['code'])) {
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    } else {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/getGoogleAccount.php';
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }
}
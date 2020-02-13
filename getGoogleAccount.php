<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope('https://www.googleapis.com/auth/plus.business.manage');
$client->setAccessType('offline');



if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    //$client->setAccessToken($_SESSION['access_token']);

    //Takes the Access Token out of the Array for use
    $userAccessTokenArray = $_SESSION['access_token'];
    $userAccessToken = $userAccessTokenArray['access_token'];

//Calls API to get a list of google accounts associated with the users Access Token
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://mybusiness.googleapis.com/v4/accounts",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $userAccessToken
        ),
    ));

    $googleAccountListString = curl_exec($curl);

    curl_close($curl);

    $googleAccountListArray = json_decode($googleAccountListString, True);

    $googleAccountID = $googleAccountListArray['accounts']['0']['name'];

    ?>

    <div class="container">
        <div>
            
        </div>
    </div>

    <?php

    foreach ($googleAccountListArray['accounts'] as $googleAccountListItem){
        print_r($googleAccountListItem);
    }


} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
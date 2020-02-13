<?php
require_once __DIR__.'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope('https://www.googleapis.com/auth/plus.business.manage');
$client->setAccessType('offline');

//$client->revokeToken();
//unset($_SESSION['access_token']);


if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    //$client->setAccessToken($_SESSION['access_token']);



    //Takes the Access Token out of the Array for use
    $userAccessTokenArray = $_SESSION['access_token'];
    $userAccessToken = $userAccessTokenArray['access_token'];







    function googleMyBusinessAPIGetListofLocations($userAccessToken, $googleAccountID){
        //this api call uses there account ID to go and look for all business locations that are tied to there google account.
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mybusiness.googleapis.com/v4/" . $googleAccountID . "/locations",
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

        $googleAccountLocationListString = curl_exec($curl);

        curl_close($curl);

        $googleAccountLocationListArray = json_decode($googleAccountLocationListString, True);
        $accountIDandLocationID = $googleAccountLocationListArray['locations']['0']['name'];

        return $accountIDandLocationID;

    }
    //We need to have the users choose the location that they want to link to there account with this API call. If they do not have any business listings then we need to tell them that they need to create a listing or they need to unlink there account and link it to a different google account. Also give them the option to contact support if they belive this is an error


    //$locationName = $googleAccountLocationListArray['locations']['0']['locationName'];
    //$locationAddress = $googleAccountLocationListArray['locations']['0']['address']['addressLines']['0'] . ', ' . $googleAccountLocationListArray['locations']['0']['address']['locality'] . ', ' . $googleAccountLocationListArray['locations']['0']['address']['administrativeArea'] . ' ' . $googleAccountLocationListArray['locations']['0']['address']['postalCode'];
    //$locationPhoneNumber = $googleAccountLocationListArray['locations']['0']['primaryPhone'];



    function googleMyBusinessAPIGetListofReviews($userAccessToken, $accountIDandLocationID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mybusiness.googleapis.com/v4/" . $accountIDandLocationID . "/reviews",
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

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;
    }

    $googleAccountID = googleMyBusinessAPIGetListofAccounts($userAccessToken);
    $accountIDandLocationID = googleMyBusinessAPIGetListofLocations($userAccessToken, $googleAccountID);
    googleMyBusinessAPIGetListofReviews($userAccessToken, $accountIDandLocationID);

    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/connectGoogleAccount.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));


} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google-oauth2-test/connectGoogleAccount.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
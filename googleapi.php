<?php

require_once 'vendor/autoload.php';

// init configuration
$clientID = '533117104250-n1sns9pljvnaqd6lfj6ttmpsbod6cljl.apps.googleusercontent.com';
$clientSecret = 'aHETHD77QjsiNWJVOFbgJQ7Z';
$redirectUri = 'http://localhost/src/Client/index.html?id=11';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope(Google_Service_Calendar::CALENDAR);


// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);


    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;

    $service = new Google_Service_Calendar($client);
    $calendarId = 'primary';
    $event = new Google_Service_Calendar_Event(array(
        'summary' => "dsa",
        'location' => "ROMANiAAA",
        'description' => 'A chance to hear more about Google\'s developer products.',
        'start' => array(
            'dateTime' => '2020-12-28T09:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
        ),
        'end' => array(
            'dateTime' => '2020-11-29T09:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
        ),
        'recurrence' => array(
            'RRULE:FREQ=DAILY;COUNT=2'
        ),
        'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
        ),
        'reminders' => array(
            'useDefault' => FALSE,
            'overrides' => array(
                array('method' => 'email', 'minutes' => 24 * 60),
                array('method' => 'popup', 'minutes' => 10),
            ),
        ),
    ));


    $service->events->insert($calendarId, $event);


    // now you can use this profile info to create account in your website and make user logged in.#


} else {
    echo $client->createAuthUrl();

}







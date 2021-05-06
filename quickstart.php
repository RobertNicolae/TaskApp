<?php
require  'vendor/autoload.php';


//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('533117104250-n1sns9pljvnaqd6lfj6ttmpsbod6cljl.apps.googleusercontent.com
');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('aHETHD77QjsiNWJVOFbgJQ7Z');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('http://localhost/src/Client/');

//
$google_client->addScope('email');

$google_client->addScope('profile');

//start session on web page
session_start();



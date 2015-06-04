<?php

require "twilio-php-master/Services/Twilio.php";
require_once "config.php";


$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE, 3306);

if($mysqli->connect_error) {
    error_log("Can't connect to Database. Error:" . $mysqli->connect_error);
    exit(1);
}

$client = new Services_Twilio(ACCOUNT_SID, AUTH_TOKEN);


//$target = "ryan"; // temp set static target
$message = $_REQUEST['Message'];
$target = ltrim(strtok($message, " "), "@";


if($_REQUEST['To'] == US_NUMBER) {
    $outboundNumber = FRENCH_NUMBER;
    $targetPhone = "french_phone";
} else if($_REQUEST['To'] == FRENCH_NUMBER) {
    $outboundNumber = US_NUMBER;
    $targetPhone = "us_phone";
} else {
    error_log("MSG received on unknown phone number. Number: ".$_REQUEST['To']. " Expected: ".FRENCH_NUMBER );
    $mysqli->close();
    exit(1); //Kill the program if source can't be reliably confirmed
}

    
if($result = $mysqli->query("SELECT ".$targetPhone." FROM users WHERE handle = '".$target."'")) {
    $resultArray = $result->fetch_array();
    $targetPhoneNumber = "+".$resultArray[$targetPhone];
} else {
    echo $mysqli->error;
    error_log($mysqli->error);
}

$mysqli->close();

try {
    $message = $client->account->messages->create(array(
        "From" => $outboundNumber,
        "To" => $targetPhoneNumber,
        "Body" => $_REQUEST['Body'],
    ));
} catch (Services_Twilio_RestException $e) {
    echo $e->getMessage();
    error_log($e->getMessage());
}

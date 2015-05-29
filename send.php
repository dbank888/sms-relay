<?php

require "twilio-php-master/Services/Twilio.php";
require_once "config.php";




$mysqli = new mysqli($HOST, $USER, $PASSWORD, $DATABASE);
$client = new Services_Twilio($ACCOUNT_SID, $AUTH_TOKEN);

$target = "ryan"; // temp set static target
if($_REQUEST['To'] == $US_NUMBER) {
    $outboundNumber = $FRENCH_NUMBER;
    $targetPhone = "french_phone";
} else if($_REQUEST['To'] == $FRENCH_NUMBER) {
    $outboundNumber = $US_NUMBER;
    $targetPhone = "us_phone";
} else {
    error_log("MSG received on unknown phone number. Number: ".$_REQUEST['To']. " Expected: ".$FRENCH_NUMBER );
    $mysqli->close();
    exit(1); //Kill the program if source can't be reliably confirmed
}

    
if($result = $mysqli->query("SELECT ".$targetPhone." FROM users WHERE handle=".$target)) {
    $obj = $result->fetch_object();
    $targetPhoneNumber = $obj[$targetPhone];
} else {
    echo $mysqli->error;
    error_log($mysqli->error);
}

$mysqli->close();

try {
    $message = $client->account->messages->create(array(
        "From" => $outboundNumber,
        "To" => "+16785173393",
        "Body" => $_REQUEST['Body'],
    ));
} catch (Services_Twilio_RestException $e) {
    echo $e->getMessage();
    error_log($e->getMessage());
}
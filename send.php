<?php

require "twilio-php-master/Services/Twilio.php";
require_once "credentials.php";


$client = new Services_Twilio($AccountSid, $AuthToken);

try {
    $message = $client->account->messages->create(array(
        "From" => "+14703446969",
        "To" => "+16785173393",
        "Body =>"Test message!",
    ));
} catch (Services_Twilio_RestException $e) {
    echo $e->getMessage();
}
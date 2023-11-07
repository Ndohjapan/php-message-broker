<?php
require_once 'Producer.php';

// Instantiate Producer
$producer = new Producer();

// Call the publishMessage method
$routingKey = 'feeding';
$payment = array(
    "timeOfPayment" => date('d-m-Y h:i:s a'),
    "routingKey" => "feeding",
    "payment" => array(
        "amountPaid" => 605000
    ),
    "user" => array(
        "_id" => "6499e92b36a1b49821308a06",
        "matricNumber" => "LCU/UG/20/17101",
        "email" => "user12@mail.com",
        "firstname" => "Joel1",
        "lastname" => "Ndoh1",
        "middlename" => "Chibueze1",
        "faculty" => "Basic Medical & Applied Science",
        "department" => "Software Engineering",
        "programme" => "Software Engineering",
        "level" => "300",
        "sex" => "Male",
        "modeOfStudy" => "Full Time",
        "qualification" => "BSc",
        "phoneNumber" => "0900000000",
    )
);

$producer->publishMessage($routingKey, $payment);

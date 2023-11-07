<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
  private $channel;

  public function createChannel()
  {

    // Replace with rabbitmq url using environment variables
    // $url = 'amqp://guest:guest@localhost:5672';

    $url = "amqps://lvwbkrpj:BvBtEfbH8-1Y5qDPrd9nZ9a-OOHWec7O@toad.rmq.cloudamqp.com/lvwbkrpj?heartbeat=60";

    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'];
    if (isset($parsedUrl['port'])) {
      $port = $parsedUrl['port'];
    } else {

      $port = 5672;
    }
    $user = $parsedUrl['user'];
    $pass = $parsedUrl['pass'];
    $vhost = $parsedUrl['path'] ? $user : false;

    $connection = "";

    if ($vhost) {
      $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
    } else {
      $connection = new AMQPStreamConnection($host, $port, $user, $pass);
    }

    $this->channel = $connection->channel();
  }


  public function publishMessage($routingKey, $payment)
  {

    // Create a channel or use existing channel
    if (!$this->channel) {
      $this->createChannel();
    }

    // Use environment variables to store the value
    $exchangeName = 'schoolExchange';
    $this->channel->exchange_declare($exchangeName, 'direct', false, true, false);

    $paymentDetails = $payment;

    $message = new AMQPMessage(json_encode($paymentDetails));

    // Register a callback for delivery confirmations (publisher confirms)
    $this->channel->set_ack_handler(function (AMQPMessage $message) use ($routingKey) {
      echo 'The message for ' . $routingKey . ' has been acknowledged by the consumer.' . PHP_EOL;
    });

    // Enable publisher confirms
    $this->channel->confirm_select();


    $this->channel->basic_publish($message, $exchangeName, $routingKey);

    echo 'The new ' . $routingKey . ' payment is sent to exchange <br>';

    // Wait for a confirmation from the server
    if ($this->channel->wait_for_pending_acks()) {
      echo 'The new ' . $routingKey . ' payment is sent to exchange.' . PHP_EOL;
    }
  }
}

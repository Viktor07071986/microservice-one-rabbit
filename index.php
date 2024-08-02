<?php

    require_once __DIR__ . '/vendor/autoload.php';

    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Exchange\AMQPExchangeType;
    use PhpAmqpLib\Message\AMQPMessage;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $queue = "RabbitMQQueue";
        $exchange = "amq.direct";
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest', '/', false, 'AMQPLAIN', null, 'en_US', 30);
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);
        $messageBody = json_encode($_POST);
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);
        header('Location: '.$_SERVER['REQUEST_URI']);
    }

?>

<form action="<?=$_SERVER["REQUEST_URI"];?>" method='POST' style='margin-top: 25px;'>
    Логин:<br/><input type="text" name="firstname" required><br/>
    Заголовок сообщения:<br/><input type="text" name="header_message" required><br/>
    Сообщение:<br/><textarea rows="10" cols="45" name="text_message" required></textarea><br/>
    <input type="hidden" name="date_message" value="<?=date("Y-m-d H:i:s");?>"><br/>
    <input type="submit" value="Отправить"/>
</form>
<?php
namespace Hook;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

class SyncHook
{

    private $connection;
    private $channel;

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'password', '/');
            $this->channel = $this->connection->channel();
            $this->channel->queue_declare('fossbilling_updates', false, true, false, false);
        } catch (\Exception $e) {
            error_log('Failed to connect to RabbitMQ: ' . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function onBeforeAdminCreateClient($di, $data)
    {
        error_log('onBeforeAdminCreateClient: ' . json_encode($data));
        $this->send_message('wordpress', 'create', $data['params']);
    }

    public function onBeforeAdminClientUpdate($di, $data)
    {
        error_log('onBeforeAdminClientUpdate: ' . json_encode($data));
        $this->send_message('wordpress', 'update', $data['params']);
    }

    public function onBeforeAdminClientDelete($di, $data)
    {
        $id = $data['params']['id'];
        $model = $di['db']->findOne('client', 'id = ?', [$id]);
        $email = $model['email'];
        error_log("onBeforeAdminClientDelete, email: " . $email);
        $this->send_message('wordpress', 'delete', $email);
    }

    public function send_message($target, $action, $client) {
        $json = json_encode([
            'target' => $target,
            'action' => $action,
            'client' => $client
        ]);
        error_log('Sending message to RabbitMQ: ' . $json);
        $message = new AMQPMessage($json);
        $this->channel->basic_publish($message, '', 'fossbilling_updates');
    }
}
?>

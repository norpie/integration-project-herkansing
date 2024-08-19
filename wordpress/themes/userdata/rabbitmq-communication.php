<?php
/**
 * Plugin Name: Customer Sync Plugin
 * Description: Syncs customer data with microservice via RabbitMQ
 */

require_once(__DIR__ . '/vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CustomerSyncPlugin {
    private $connection;
    private $channel;

    public function __construct() {
        $this->setup_rabbitmq_connection();
    }

    private function setup_rabbitmq_connection() {
        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'password', '/');
            $this->channel = $this->connection->channel();
            $this->channel->queue_declare('customer_sync', false, true, false, false);
            $this->channel->basic_consume('customer_sync', '', false, true, false, false, function($message) {
                process_message($message);
            });
            error_log('Connected to RabbitMQ, waiting for messages');
        } catch (\Exception $e) {
            // Log the error
            error_log('Failed to connect to RabbitMQ: ' . $e->getMessage());
            // Optionally, you could set a flag to indicate that RabbitMQ is unavailable
            $this->rabbitmq_available = false;
        }
    }

    private function send_message($action, $post_id) {
        $message = new AMQPMessage(json_encode([
            'action' => $action,
            'customer_id' => $post_id,
            'data' => get_post_meta($post_id)
        ]));
        $this->channel->basic_publish($message, '', 'customer_sync');
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}


// Handle rabbitmq consumed message, check if `target` is wordpress and process the message
function process_message($message) {
    $target = $message['target'];
    if ($target == 'wordpress') {
        // Log
        error_log('Processing message for wordpress');
    }
}

// Create public instance of the plugin
$customer_sync_plugin = new CustomerSyncPlugin();

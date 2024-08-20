<?php
// Add custom post type for a fossbilling client
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
            $this->channel->queue_declare('fossbilling_updates', false, true, false, false);
            error_log('Connected to RabbitMQ');
            // Consume all messages from the queue, and continue
            while (true) {
                $messageCount = $this->channel->queue_declare('fossbilling_updates', true)[1];
                if ($messageCount == 0) {
                    break;
                }
                $msg = $this->channel->basic_get('fossbilling_updates');
                process_message($msg);
            }
            $this->rabbitmq_available = true;
        } catch (\Exception $e) {
            // Log the error
            error_log('Failed to connect to RabbitMQ: ' . $e->getMessage());
            // Optionally, you could set a flag to indicate that RabbitMQ is unavailable
            $this->rabbitmq_available = false;
        }
    }

    public function send_message($target, $action, $client) {
        $json = json_encode([
            'target' => $target,
            'action' => $action,
            'client' => $client
        ]);
        error_log('Sending message to RabbitMQ: ' . $json);
        $message = new AMQPMessage($json);
        $this->channel->basic_publish($message, '', 'customer_sync');
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}

// Handle rabbitmq consumed message, check if `target` is wordpress and process the message
function process_message($message) {
    $obj = json_decode($message->body, true);
    $action = $obj['action'];
    $client = $obj['client'];
    error_log('Processing message for wordpress: ' . $action);
    switch ($action) {
        case 'create':
            create_client($client);
            break;
        case 'update':
            update_client($client);
            break;
        case 'delete':
            delete_client($client);
            break;
        default:
            error_log('Unknown action: ' . $action);
            break;
    }
    error_log('Acknowledging message');
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
}

function initiate_rabbitmq_connection() {
    global $customer_sync_plugin;
    $customer_sync_plugin = new CustomerSyncPlugin();
}

add_action('init', 'initiate_rabbitmq_connection');

function add_client_post_type() {
    $labels = array(
        'name' => 'Clients',
        'singular_name' => 'Client',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Client',
        'edit_item' => 'Edit Client',
        'new_item' => 'New Client',
        'all_items' => 'All Clients',
        'view_item' => 'View Client',
        'search_items' => 'Search Clients',
        'not_found' =>  'No Clients found',
        'not_found_in_trash' => 'No Clients found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Clients'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'client' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'client', $args );
}

add_action( 'init', 'add_client_post_type' );

function handle_form_submit_new_client() {
    if (!isset($_POST['submit_client_form'])) {
        return;
    }
    $client = [
        'first_name' => sanitize_text_field($_POST['client_first_name']),
        'last_name' => sanitize_text_field($_POST['client_last_name']),
        'company' => sanitize_text_field($_POST['client_company']),
        'address' => sanitize_text_field($_POST['client_address']),
        'city' => sanitize_text_field($_POST['client_city']),
        'state' => sanitize_text_field($_POST['client_state']),
        'country' => sanitize_text_field($_POST['client_country']),
        'postal_code' => sanitize_text_field($_POST['client_postal_code']),
        'phone' => sanitize_text_field($_POST['client_phone']),
        'currency' => sanitize_text_field($_POST['client_currency']),
        'email' => sanitize_text_field($_POST['client_email']),
        'password' => sanitize_text_field($_POST['client_password'])
    ];

    // Insert a new post of type 'fossbilling_client'
    $client_id = new_client($client);
    if ($client_id) {
        echo '<p>Client added successfully!</p>';
    } else {
        echo '<p>Client alreay exists.</p>';
        return;
    }
    global $customer_sync_plugin;
    $customer_sync_plugin->send_message('fossbilling', 'create', $client);
}

add_action('wp', 'handle_form_submit_new_client');

function handle_form_submit_delete_client() {
    if (!isset($_POST['client_id'])) {
        return;
    }
    $client_id = sanitize_text_field($_POST['client_id']);
    $client_email = get_post_meta($client_id, 'client_email', true);
    delete_client($client_email);
    global $customer_sync_plugin;
    $customer_sync_plugin->send_message('fossbilling', 'delete', $client_email);
    echo '<p>Client deleted successfully!</p>';
}

add_action('wp', 'handle_form_submit_delete_client');

function new_client($client) {
    $client_first_name = $client['first_name'];
    $client_last_name = $client['last_name'];
    $client_company = $client['company'];
    $client_address = $client['address'];
    $client_city = $client['city'];
    $client_state = $client['state'];
    $client_country = $client['country'];
    $client_postal_code = $client['postal_code'];
    $client_phone = $client['phone'];
    $client_currency = $client['currency'];
    $client_email = $client['email'];
    $client_password = $client['password'];

    // Check if email already exists
    $args = array(
        'post_type' => 'client',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'client_email',
                'value' => $client_email,
                'compare' => '='
            )
        )
    );

    $clients = get_posts($args);
    if (count($clients) > 0) {
        return null;
    }

    // Insert a new post of type 'fossbilling_client'
    $client_id = wp_insert_post(array(
        'post_title' => $client_email,
        'post_type' => 'client',
        'post_status' => 'publish',
        'meta_input' => array(
            'client_first_name' => $client_first_name,
            'client_last_name' => $client_last_name,
            'client_company' => $client_company,
            'client_address' => $client_address,
            'client_city' => $client_city,
            'client_state' => $client_state,
            'client_country' => $client_country,
            'client_postal_code' => $client_postal_code,
            'client_phone' => $client_phone,
            'client_currency' => $client_currency,
            'client_email' => $client_email,
            'client_password' => $client_password
        ),
    ));
    return $client_id;
}

function delete_client($email) {
    $args = array(
        'post_type' => 'client',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'client_email',
                'value' => $email,
                'compare' => '='
            )
        )
    );

    $clients = get_posts($args);
    foreach ($clients as $client) {
        wp_delete_post($client->ID, true);
    }
}

function edit_client($client) {
    $client_email = $client['email'];
    $args = array(
        'post_type' => 'client',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'client_email',
                'value' => $client_email,
                'compare' => '='
            )
        )
    );

    $clients = get_posts($args);
    if (count($clients) == 0) {
        return null;
    }
    $old_client = $clients[0];

    $client_first_name = $client['first_name'];
    $client_last_name = $client['last_name'];
    $client_company = $client['company'];
    $client_address = $client['address'];
    $client_city = $client['city'];
    $client_state = $client['state'];
    $client_country = $client['country'];
    $client_postal_code = $client['postal_code'];
    $client_phone = $client['phone'];
    $client_currency = $client['currency'];
    $client_password = $client['password'];

    // Update the post of type 'fossbilling_client'
    $client_id = wp_update_post(array(
        'ID' => $old_client->ID,
        'post_title' => $client_email,
        'post_type' => 'client',
        'post_status' => 'publish',
        'meta_input' => array(
            'client_first_name' => $client_first_name,
            'client_last_name' => $client_last_name,
            'client_company' => $client_company,
            'client_address' => $client_address,
            'client_city' => $client_city,
            'client_state' => $client_state,
            'client_country' => $client_country,
            'client_postal_code' => $client_postal_code,
            'client_phone' => $client_phone,
            'client_currency' => $client_currency,
            'client_email' => $client_email,
            'client_password' => $client_password
        ),
    ));
    return $client_id;
}
?>

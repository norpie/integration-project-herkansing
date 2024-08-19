<?php
/* Template Name: Clients */
/* This page shows all current clients,
 * gives the option to edit existing client,
 * and create new ones. The clients should be
 * saved as a custom post type.
*/

get_header();
?>

<html>
<head>
    <title>Clients</title>
</head>
<body>
    <h1>Clients</h1>
    <table>
        <tr>
            <th>Client ID</th>
            <th>Client First Name</th>
            <th>Client Last Name</th>
            <th>Client Company</th>
            <th>Client Address</th>
            <th>Client City</th>
            <th>Client State</th>
            <th>Client Country</th>
            <th>Client Postal Code</th>
            <th>Client Phone</th>
            <th>Client Currency</th>
            <th>Client Email</th>
            <th>Client Password</th>
        </tr>
        <?php
        $args = array(
            'post_type' => 'client',
            'post_status' => 'publish',
            'posts_per_page' => -1
        );

        $clients = get_posts($args);
        foreach ($clients as $client) {
            $client_id = $client->ID;
            $client_first_name = get_post_meta($client_id, 'client_first_name', true);
            $client_last_name = get_post_meta($client_id, 'client_last_name', true);
            $client_company = get_post_meta($client_id, 'client_company', true);
            $client_address = get_post_meta($client_id, 'client_address', true);
            $client_city = get_post_meta($client_id, 'client_city', true);
            $client_state = get_post_meta($client_id, 'client_state', true);
            $client_country = get_post_meta($client_id, 'client_country', true);
            $client_postal_code = get_post_meta($client_id, 'client_postal_code', true);
            $client_phone = get_post_meta($client_id, 'client_phone', true);
            $client_currency = get_post_meta($client_id, 'client_currency', true);
            $client_email = get_post_meta($client_id, 'client_email', true);
            $client_password = get_post_meta($client_id, 'client_password', true);

            echo "<tr>";
            echo "<td>$client_id</td>";
            echo "<td>$client_first_name</td>";
            echo "<td>$client_last_name</td>";
            echo "<td>$client_company</td>";
            echo "<td>$client_address</td>";
            echo "<td>$client_city</td>";
            echo "<td>$client_state</td>";
            echo "<td>$client_country</td>";
            echo "<td>$client_postal_code</td>";
            echo "<td>$client_phone</td>";
            echo "<td>$client_currency</td>";
            echo "<td>$client_email</td>";
            echo "<td>$client_password</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h2>Edit Client</h2>
    <form action="" method="post">
        <label for="client_id">Client ID:</label><br>
        <input type="text" name="client_id" id="client_id"><br>
        <input type="submit" value="Edit Client"><br>
    </form>

    <h2>Create New Client</h2>
    <form action="" method="post">
        <label for="client_first_name">Client First Name:</label><br>
        <input type="text" name="client_first_name" id="client_first_name"><br>
        <label for="client_last_name">Client Last Name:</label><br>
        <input type="text" name="client_last_name" id="client_last_name"><br>
        <label for="client_company">Client Company:</label><br>
        <input type="text" name="client_company" id="client_company"><br>
        <label for="client_address">Client Address:</label><br>
        <input type="text" name="client_address" id="client_address"><br>
        <label for="client_city">Client City:</label><br>
        <input type="text" name="client_city" id="client_city"><br>
        <label for="client_state">Client State:</label><br>
        <input type="text" name="client_state" id="client_state"><br>
        <label for="client_country">Client Country:</label><br>
        <input type="text" name="client_country" id="client_country"><br>
        <label for="client_postal_code">Client Postal Code:</label><br>
        <input type="text" name="client_postal_code" id="client_postal_code"><br>
        <label for="client_phone">Client Phone:</label><br>
        <input type="text" name="client_phone" id="client_phone"><br>
        <label for="client_currency">Client Currency:</label><br>
        <input type="text" name="client_currency" id="client_currency"><br>
        <label for="client_email">Client Email:</label><br>
        <input type="text" name="client_email" id="client_email"><br>
        <label for="client_password">Client Password:</label><br>
        <input type="text" name="client_password" id="client_password"><br>
        <input type="submit" name="submit_client_form" value="Add Client">
    </form>
</body>

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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 50%;
            margin: 0 auto;
        }

        form label {
            margin-top: 10px;
        }

        form input {
            margin-top: 5px;
            padding: 5px;
        }

        form input[type="submit"] {
            margin-top: 10px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        form input[type="submit"]:active {
            background-color: #3e8e41;
        }

        form input[type="text"], form input[type="email"], form input[type="password"] {
            width: 100%;
        }

        form input[type="submit"] {
            width: 50%;
            margin: 0 auto;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        form input[type="submit"]:active {
            background-color: #3e8e41;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        form input[type="submit"]:active {
            background-color: #3e8e41;
        }

        form input[type="submit"] {
            width: 50%;
            margin: 0 auto;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }


    </style>
</head>
<body>
    <h1>Clients</h1>
    <table>
        <tr>
            <th>Delete</th>
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
            <th>Save</th>
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
            echo "<td><form action='' method='post'><input type='hidden' name='client_id' value='$client_id'><input type='submit' value='X'></form></td>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='client_id' value='$client_id'>";
            echo "<td>$client_id</td>";
            echo "<td><input type='text' name='client_first_name' value='$client_first_name'></td>";
            echo "<td><input type='text' name='client_last_name' value='$client_last_name'></td>";
            echo "<td><input type='text' name='client_company' value='$client_company'></td>";
            echo "<td><input type='text' name='client_address' value='$client_address'></td>";
            echo "<td><input type='text' name='client_city' value='$client_city'></td>";
            echo "<td><input type='text' name='client_state' value='$client_state'></td>";
            echo "<td><input type='text' name='client_country' value='$client_country'></td>";
            echo "<td><input type='text' name='client_postal_code' value='$client_postal_code'></td>";
            echo "<td><input type='text' name='client_phone' value='$client_phone'></td>";
            echo "<td><input type='text' name='client_currency' value='$client_currency'></td>";
            echo "<td>$client_email</td>";
            echo "<td><input type='password' name='client_password' value='$client_password'></td>";
            echo "<td><input type='submit' name='submit_client_form' value='Save'></td>";
            echo "</form>";
            echo "</tr>";
        }
        ?>
    </table>

    <div class="new">
        <h2>Create New Client</h2>
        <form action="" method="post">
            <label for="client_first_name">Client First Name:</label><br>
            <input value="Konsta" type="text" name="client_first_name" id="client_first_name"><br>
            <label for="client_last_name">Client Last Name:</label><br>
            <input value="Kuosmanen" type="text" name="client_last_name" id="client_last_name"><br>
            <label for="client_company">Client Company:</label><br>
            <input value="N/A" type="text" name="client_company" id="client_company"><br>
            <label for="client_address">Client Address:</label><br>
            <input value="Rue Locquenghien 46" type="text" name="client_address" id="client_address"><br>
            <label for="client_city">Client City:</label><br>
            <input value="Bruxelles" type="text" name="client_city" id="client_city"><br>
            <label for="client_state">Client State:</label><br>
            <input value="Bruxelles" type="text" name="client_state" id="client_state"><br>
            <label for="client_country">Client Country:</label><br>
            <input value="BE" type="text" name="client_country" id="client_country"><br>
            <label for="client_postal_code">Client Postal Code:</label><br>
            <input value="1000" type="text" name="client_postal_code" id="client_postal_code"><br>
            <label for="client_phone">Client Phone:</label><br>
            <input value="0499999999" type="text" name="client_phone" id="client_phone"><br>
            <label for="client_currency">Client Currency:</label><br>
            <input value="EUR" type="text" name="client_currency" id="client_currency"><br>
            <label for="client_email">Client Email:</label><br>
            <input value="konsta@kuosmanen.dev" type="email" name="client_email" id="client_email"><br>
            <label for="client_password">Client Password:</label><br>
            <input value="Password!321" type="password" name="client_password" id="client_password"><br>
            <input type="submit" name="submit_client_form" value="Add Client">
        </form>
    </div>
</body>

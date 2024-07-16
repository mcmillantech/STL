<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	logcheck.php
//		Check logon
//
//  Author	John McMillan, McMillan Technology
// ------------------------------------------------------

    require_once 'common.php';

    $contentx = trim(file_get_contents("php://input"));
    $content = stripslashes($contentx);
    $params = json_decode($content);

    $account = addslashes($params->account);
    $password = $params->password;

    $config = setConfig();              // Connect to database
    $dbConnection = dbConnect($config);
    $mysqli = dbConnect($config);

    $sql = "SELECT * FROM customers WHERE Account='$account'";
    $result = $mysqli->query($sql);
    $rowcount = mysqli_num_rows($result);
    
    $reply = array();               // Create array for return
    if ($rowcount == 0) {           // Error - user not found
        $reply['state'] = 'Error';
        $reply['message'] = 'Invalid account';
    } else {
        $customer = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($customer['Password'] == $password) {
            $reply['state'] = 'OK';
            $reply['customer'] = $customer;
        } else {
            $reply['state'] = 'Error';
            $reply['message'] = 'Invalid password';
        }
    }

    echo json_encode($reply);       // Send JSON back to client

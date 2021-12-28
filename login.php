<?php

// This file is in charge of Login Process by sending the info / data to the server and receiving the feedback / status from the server


// STEP 1. Receive data / info passed to current file
if (empty($_REQUEST['email']) || empty($_REQUEST['password'])) {
    $return['status'] = '400';
    $return['message'] = 'Missing required information';
    echo json_encode($return);
    return;
}

// securing received info / data from hackers or injections
$email = htmlentities($_REQUEST['email']);
$password = htmlentities($_REQUEST['password']);


// STEP 2. Establish connection with the server
require('secure/access.php');
$access = new access('localhost', 'root', '', 'fb');
$access->connect();


// STEP 3. Check existance of the user. Try to fetch user with the same Email address
$user = $access->selectUser($email);


// user is found
if ($user) {
    
    // STEP 3.1 get encrypted password and salt from the server for validation
    $encryptedPassword = $user['password'];
    $salt = $user['salt'];
    
    // STEP 3.2 compare entered password by user from app/website; encrypting password; comparing the result with the result stored in the server;
    if ($encryptedPassword == sha1($password . $salt)) {

        // usere gelen null değerleri "" e çevirme
        $user = array_map( function($v) {
            return (is_null($v)) ? "" : $v;
        }, $user);
        
        // preparing information to be thrown back to the user in JSON
        $return['status'] = '200';
        $return['message'] = 'Logged in successfully';
        $return['id'] = $user['id'];
        $return['email'] = $user['email'];
        $return['firstName'] = $user['firstName'];
        $return['lastName'] = $user['lastName'];
        $return['birthday'] = $user['birthday'];
        $return['gender'] = $user['gender'];
        $return['cover'] = $user['cover'];
        $return['ava'] = $user['ava'];
        //$return['bio'] = $user['bio'];

      
        
    // encrypted password and salt do not match what user is entering as password
    } else {
        
        $return['status'] = '201';
        $return['message'] = 'Passwords do not match';
        
    }
     
// user isn't found
} else {
    
    $return['status'] = '401';
    $return['message'] = 'User is not found';
    
}


// stop connection with the server
$access->disconnect();

// pass info as JSON
echo json_encode($return);
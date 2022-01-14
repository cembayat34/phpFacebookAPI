<?php

// This class is in charge of sending new user (registartion) information to the server

// STEP 1. Receiving data passed to current PHP file. Executing IF statement. 
// If not all data has been passed, return / stop execution by throwing JSON message
if (empty($_REQUEST['email']) || empty($_REQUEST['firstName']) || empty($_REQUEST['lastName']) || 
empty($_REQUEST['password']) || empty($_REQUEST['birthday']) || empty($_REQUEST['gender'])) {
    
    $return['status'] = '400';
    $return['message'] = 'Missing user information';
    echo json_encode($return);
    return;
    
}

// using safe method to cast received data in current PHP 
$email = htmlentities($_REQUEST['email']);
$firstName = htmlentities($_REQUEST['firstName']);
$lastName = htmlentities($_REQUEST['lastName']);
$password = htmlentities($_REQUEST['password']);
$birthday = htmlentities($_REQUEST['birthday']);
$gender = htmlentities($_REQUEST['gender']);

// generating random 100 chars pseudo
$salt = openssl_random_pseudo_bytes(100);
$encryptedPassword = sha1($password . $salt);


// STEP 2. Establish Connection with the Server
// including access.php file/class
require('secure/access.php');
$access = new access('localhost', 'root', '', 'fb');
$access->connect();


// STEP 3. Check availability of the login / user information

$user = $access->selectUser($email);



// found user with the same Email address
if (!empty($user)) {
    
    // throw back JSON to user
    $return['status'] = '400';
    $return['message'] = 'The Email is already registered';
    echo json_encode($return);
    
} else {
    
    // STEP 4. Send request to Insert the data in the server
    $result = $access->insertUser($email, $firstName, $lastName, $encryptedPassword, $salt, $birthday, $gender);
    
    // result is positive - inserted

    
    
    if ($result) {
        
        // select currently inserted user
        $user = $access->selectUser($email);

        // usere gelen null değerleri "" e çevirme
        $user = array_map( function($v) {
            return (is_null($v)) ? "" : $v;
        }, $user);
        
        // throw back the user details
        $return['status'] = '200';
        $return['message'] = 'Successfully registered';
        $return['id'] = $user['id'];
        $return['email'] = $email;
        $return['firstName'] = $firstName;
        $return['lastName'] = $lastName;
        $return['birthday'] = $birthday;
        $return['gender'] = $gender;
        $return['ava'] = $user['ava'];
        $return['cover'] = $user['cover'];
        $return['bio'] = $user['bio'];
    
    // result is negative - couldn't insert
    } else {
        
        $return['status'] = '400';
        $return['message'] = 'Could not insert information';   
    }
    
}


echo json_encode($return);        
$access->disconnect();
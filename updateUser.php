<?php
 
//This fils is responsible for updating user related infromation in the server

// STEP 1. Receiving passed infromation to current file
if (empty($_REQUEST['id']) || empty($_REQUEST['email']) || empty($_REQUEST['firstName']) || empty($_REQUEST['lastName']) 
        || empty($_REQUEST['birthday']) || !isset($_REQUEST['gender'])) {
    
    $return['status'] = '400';
    $return['message'] = 'Missing required information';
    echo json_encode($return);
    return;
    
}

// secure infromation and store in vars
$id = htmlentities($_REQUEST['id']);
$email = htmlentities($_REQUEST['email']);
$firstName = htmlentities($_REQUEST['firstName']);
$lastName = htmlentities($_REQUEST['lastName']);
$birthday = htmlentities($_REQUEST['birthday']);
$gender = htmlentities($_REQUEST['gender']);



// STEP 2. Establish connection
require('secure/access.php');
$access = new access('localhost', 'root', '', 'fb');
$access->connect();


// STEP 3. Update user information
$result = $access->updateUser($email, $firstName, $lastName, $birthday, $gender, $id);

if ($result) {
    
    // updated successfully
    $return['status'] = '200';
    $return['message'] = 'User is updated successfully';
    
    
    // STEP 4. Update passowrd
    if ($_REQUEST['newPassword'] == 'true') {
        
        // receiving new password. generating new salt. generating new Super Securerd password
        $password = htmlentities($_REQUEST['password']);
        $salt = openssl_random_pseudo_bytes(100);
        $dbpassword = sha1($password . $salt);
        
        // updating password in database
        $passwordChanged = $access->updatePassword($id, $dbpassword, $salt);
        
        // logic of scenarious
        if ($passwordChanged) {
            $return['password'] = 'Password is changed successfully';
        } else {
            $return['password'] = 'Password could not be changed';
        }
        
    }
    
    // STEP 5. Return back user related information
    $user = $access->selectUserID($id);
    
    // logic of scenarious
    if ($user) {
        
        $return['id'] = $user['id'];
        $return['email'] = $user['email'];
        $return['firstName'] = $user['firstName'];
        $return['lastName'] = $user['lastName'];
        $return['birthday'] = $user['birthday'];
        $return['gender'] = $user['gender'];
        $return['cover'] = $user['cover'];
        $return['ava'] = $user['ava'];
        $return['bio'] = $user['bio'];
        
    } else {
        $return['status'] = '400';
        $return['message'] = 'Could not complete the process';
    }
    
    
} else {
    
    // can not update
    $return['status'] = '400';
    $return['message'] = 'Could not update user';
    
}


// STEP 7. Shut down
$access->disconnect();
echo json_encode($return);

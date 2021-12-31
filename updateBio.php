<?php

// STEP 1. eksik bilgi var mı kontrol et

if (empty($_REQUEST['id'])){
    $return['status'] = '400';
    $return['message'] = 'Missing Required information';
    echo json_encode($return);
    return;
}

// bodyden id ve bioyu güvenli bi şekilde al
$id = htmlentities($_REQUEST['id']);
$bio = htmlentities($_REQUEST['bio']);


// STEP 2. establish connection
require('secure/access.php');
$access = new access('localhost','root','','fb');
$access->connect();





// STEP 3. update bio
$result = $access->updateBio($id,$bio);

//updated successfuly
if ($result) {

    // select user and throw back to the user info
    $user = $access->selectUserID($id);

    // usere gelen null değerleri "" e çevirme
    $user = array_map( function($v) {
        return (is_null($v)) ? "" : $v;
    }, $user);

    if ($user) {
        $return['status'] = '200';
        $return['message'] = 'Bio has been updated';
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
        $return['message'] = 'User is not found';
    }

    

// error while updating
} else {

    $return['status'] = '400';
    $return['message'] = 'Unable to update bio';
}

echo json_encode($return);
$access->disconnect();


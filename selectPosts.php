<?php

// this files sends request to the server for retrieving all the posts related to the certain user.

// STEP 1. receive passed variables / information
if (!isset($_REQUEST['id']) && !isset($_REQUEST['limit']) && !isset($_REQUEST['offset'])) {

    $return['status'] = '400';
    $return['message'] = 'Missing required information';
    echo json_encode($return);
    return;
}


// secure received variables / information
$id = htmlentities($_REQUEST['id']);
$limit = htmlentities($_REQUEST['limit']);
$offset = htmlentities($_REQUEST['offset']);


// STEP 2. establish connection
require('secure/access.php');
$access = new access('localhost', 'root', '', 'fb');
$access -> connect();


// STEP 3. select posts from the server
$posts = $access->selectPost($id, $offset, $limit);

if ($posts) {
    $return['posts'] = $posts;
} else {
    $return['message'] = 'Could not find posts';
}


// STEP 4. disconnect
echo json_encode($return);
$access -> disconnect();


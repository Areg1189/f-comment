<?php

require __DIR__ . '/vendor/autoload.php';

use Quick\Quick;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if (!(bool) getenv('FACEBOOK_VERIFIED')) {
    if ($_GET['hub_verify_token'] == getenv('FACEBOOK_SECRET')) {
        echo $_GET['hub_challenge'];
    }
    die();
}

$app = new Quick;

$app->run(json_decode(file_get_contents('php://input'), true));

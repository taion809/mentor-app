<?php
require_once('../../composer/vendor/autoload.php');
$app = new \Slim\Slim();
// define the routes - http://docs.slimframework.com/
$app->get('/', function() {
    print 'hello world';
});
$app->run();

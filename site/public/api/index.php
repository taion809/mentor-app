<?php
require_once('../../vendor/autoload.php');
$app = new \Slim\Slim();

// load in the config
require_once '../../config/environment.php';
require_once '../../config/database.php';

// connect to the database
$app->db = new \PDO(
    'mysql:hostname='.$config['database'][$config['environment']]['hostname'].';dbname='.$config['database'][$config['environment']]['database'],
    $config['database'][$config['environment']]['username'],
    $config['database'][$config['environment']]['password']
);

$app->get('/v1/user/:id', function() use ($app) {
    // add authentication, authz shouldn't matter here
    if (!preg_match('/^[0-9a-f]{10}$/', $id)) {
        http_response_code(404);
        return;
    }
    $userService = new \MentorApp\UserService($app->db);
    $userResponse = $userService->retrieve($id);
    $tagService = new \MentorApp\TagService($app->db);
    if ($userResponse === null) {
        http_response_code(404);
        return;
    }

    $response = array();
    $response['teaching_skills'] = array();
    $response['learning_skills'] = array();
    $response['self'] = '';
    $response['id'] = htmlspecialchars($userResponse->id);
    $response['first_name'] = htmlspecialchars($userResponse->firstName);
    $response['last_name'] = htmlspecialchars($userResponse->lastName);
    $response['email'] = htmlspecialchars($userResponse->email);
    $response['github_handle'] = htmlspecialchars($userResponse->githubHandle);
    $response['irc_nick'] = htmlspecialchars($userResponse->ircNick);
    $response['twitter_handle'] = htmlspecialchars($userResponse->twitterHandle);
    $response['mentor_available'] = htmlspecialchars($userResponse->mentorAvailable);
    $response['apprentice_available'] = htmlspecialchars($userResponse->apprenticeAvailable);
    // retrieve tag instances for the tag ids provided for teaching
    foreach ($userResponse->teachingSkills as $teaching_skill) {
        $task = $taskService->retrieve($teaching_skill);
        $response['teaching_skills'][]['self'] = '';
        $response['teaching_skills'][]['id'] = htmlspecialchars($task->id);
        $response['teaching_skills'][]['name'] = htmlspecialchars($task->name);
        $response['teaching_skills'][]['added'] = htmlspecialchars($task->added); 
    }

    foreach ($userResponse->learningSkills as $learning_skill) {
        $task = $taskService->retrieve($learning_skill);
        $response['learning_skills'][]['self'] = '';
        $response['learning_skills'][]['id'] = htmlspecialchars($task->id);
        $response['learning_skills'][]['name'] = htmlspecialchars($task->name);
        $response['learning_skills'][]['added'] = htmlspecialchars($task->added);
    }
    http_response_code(200); 
    print json_encode($response); 
});

$app->delete('/v1/user/:id, function() use ($app) {
    if (!preg_match('/^[0-9a-f]{10}$/', $id)) {
        http_response_code(404);
        return;
    }
    $userService = new \MentorApp\UserService($app->db);

    if (!$userService->delete($id)) {
        http_response_code(404);
        return;
    }
    http_response_code(200);
});
    
$app->run();

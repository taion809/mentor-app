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
    $skillService = new \MentorApp\SkillService($app->db);
    if ($userResponse === null) {
        http_response_code(404);
        return;
    }
    $userFormatter = new UserArrayFormatter($userResponse);
    $response = $userFormatter->format();
    // retrieve skill instances for the skill ids provided for teaching
    $learningSkills = $skillService->retrieveByIds($userResponse->learningSkills);
    $teachingSkills = $skillService->retrieveByIds($userResponse->teachingSkills);
    // Cut down the queries, but I still don't like this, especially for skills
    // Almost feels like it needs some sort of Object specific formatter or something.
    foreach ($learningSkills as $learningSkill) {
        $response['learning_skills'][]['self'] = '';
        $response['learning_skills'][]['id'] = $learningSkill->id;
        $response['learning_skills'][]['name'] = $learningSkill->name;
        $response['learning_skills'][]['added'] = $learningSkill->added;
    }
    foreach ($teachingSkills as $teachingSkill) {
        $response['teaching_skills'][]['self'] = '';
        $response['teaching_skills'][]['id'] = $teachingSkill->id;
        $response['teaching_skills'][]['name'] = $teachingSkill->name;
        $response['teaching_skills'][]['added'] = $teachingSkill->name;
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

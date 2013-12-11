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
    $response = array();
    $userService = new \MentorApp\UserService($app->db);
    $userResponse = $userService->retrieve($id);
    $skillService = new \MentorApp\SkillService($app->db);
    $partnershipManager = new \MentorApp\PartnershipManager($app->db);
    if ($userResponse === null) {
        http_response_code(404);
        return;
    }
    $userSerializer = new UserArraySerializer();
    $skillSerializer = new SkillArraySerializer();
    $partnershipSerializer = new PartnershipArraySerializer();
    $response = $userSerializer->toArray($userResponse);

    // retrieve skill instances for the skill ids provided for teaching
    $learningSkills = $skillService->retrieveByIds($userResponse->learningSkills);
    $teachingSkills = $skillService->retrieveByIds($userResponse->teachingSkills);
    foreach ($learningSkills as $learningSkill) {
        $response['learningSkills'][] = $skillSerializer->toArray($learningSkill);
    }
    foreach ($teachingSkills as $teachingSkill) {
        $response['teachingSkills'][] - $skillSerializer->toArray($teachingSkill);
    }

    $mentorships = $partnershipManager->retrieveByMentor($id);
    $apprenticeships = $partnershipManager->retrieveByApprentice($id);
    $response['partnerships'] = array();
    $response['partnerships']['mentoring'] = $partnershipSerializer->fromArray($mentorships);
    $response['partnerships']['apprencting'] = $partnershipSerializer->fromArray($apprenticeships); 

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

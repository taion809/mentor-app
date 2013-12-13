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
    $hashValidator = new \MentorApp\HashValidator();
    if (!$hashValidator->validate($id)) {
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
        $response['teachingSkills'][] = $skillSerializer->toArray($teachingSkill);
    }

    $mentorships = $partnershipManager->retrieveByMentor($id);
    $apprenticeships = $partnershipManager->retrieveByApprentice($id);
    $response['partnerships'] = array();
    $response['partnerships']['mentoring'] = $partnershipSerializer->fromArray($mentorships);
    $response['partnerships']['apprencting'] = $partnershipSerializer->fromArray($apprenticeships); 

    http_response_code(200); 
    print json_encode($response); 
});

$app->delete('/v1/user/:id', function() use ($app) {
    $hashValidator = new \MentorApp\HashValidator();
    if (!$hashValidator->validate($id)) {
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

$app->post('/v1/user', function use ($app) {
    $user = new \MentorApp\User();
    $userService = new \MentorApp\UserService($app->db);
    $taskService = new \MentorApp\SkillService($app->db);
    $user->firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $user->lastName = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $user->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $user->githubHandle = filter_var($_POST['github_handle'], FILTER_SANITIZE_STRING);
    $user->twitterHandle = filter_var($_POST['twitter_handle'], FILTER_SANITIZE_STRING);
    $user->ircNick = filter_var($_POST['irc_nick'], FILTER_SANITIZE_STRING);
    $user->mentorAvailable = ($_POST['mentor_available'] == 1) ? 1 : 0;
    $user->apprenticeAvailable = $_POST['apprentice_available'] ? 1 : 0;
    $user->teachingSkills = array();
    $user->learningSkills = array();
    $user->timezone = filter_var($_POST['timezone'], FILTER_SANITIZE_STRING);
    foreach ($_POST['teaching_skills'] as $teaching) {
        $id = filter_var($teaching, '/^[0-9a-f]{10}$/');
        $user->teachingSkills[] = $taskService->retrieve($id);
    }

    foreach ($_POST['learning_skills'] as $learning)
    {
        $id = filter_var($learning, '/^[0-9a-f]{10}$/');
        $user->learningSkills[] = $taskService->retrieve($id);
    } 

    $savedUser = $userService->create($user);
    if (!$savedUser) {
        http_response_code(400);
    }
    http_response_code(201);

    // response should include URI to resource
});        

$app->put('/vi/user', function use ($app) {
    $user = new \MentorApp\User();
    $userService = new \MentorApp\UserService($app->db);
    $taskService = new \MentorApp\SkillService($app->db);
    $user->firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $user->lastName = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $user->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $user->githubHandle = filter_var($_POST['github_handle'], FILTER_SANITIZE_STRING);
    $user->twitterHandle = filter_var($_POST['twitter_handle'], FILTER_SANITIZE_STRING);
    $user->ircNick = filter_var($_POST['irc_nick'], FILTER_SANITIZE_STRING);
    $user->mentorAvailable = ($_POST['mentor_available'] == 1) ? 1 : 0;
    $user->apprenticeAvailable = $_POST['apprentice_available'] ? 1 : 0;
    $user->teachingSkills = array();
    $user->learningSkills = array();
    $user->timezone = filter_var($_POST['timezone'], FILTER_SANITIZE_STRING);
    foreach ($_POST['teaching_skills'] as $teaching) {
        $id = filter_var($teaching, '/^[0-9a-f]{10}$/');
        $user->teachingSkills[] = $taskService->retrieve($id);
    }

    foreach ($_POST['learning_skills'] as $learning)
    {
        $id = filter_var($learning, '/^[0-9a-f]{10}$/');
        $user->learningSkills[] = $taskService->retrieve($id);
    } 

    $savedUser = $userService->update($user);
    if (!$savedUser) {
        http_response_code(400);
    }
    http_response_code(200);
});
 
$app->run();

<?php
/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package MentorApp;
 */
namespace MentorApp;

class UserArrayFormatter implements FormatInterface
{
    /**
     * @var \MentorApp\User $user User object to format
     */
    protected $user;

    /**
     * Constructor to set user property
     *
     * @param \MentorApp\User $user
     */
    public function __construct(\MentorApp\User $user)
    {
        $this->user = $user;
    }

    /**
     * Format method to satisfy the interface contract
     *
     * @return string representation of the user object
     */
    public function format()
    {
        $response = [];
        $response['id'] = htmlspecialchars($this->user->id);
        $response['first_name'] = htmlspecialchars($this->user->firstName);
        $response['last_name'] = htmlspecialchars($this->user->lastName);
        $response['email'] = htmlspecialchars($this->user->email);
        $response['github_handle'] = htmlspecialchars($this->user->githubHandle);
        $response['irc_nick'] = htmlspecialchars($this->user->ircNick);
        $response['twitter_handle'] = htmlspecialchars($this->user->twitterHandle);
        $response['mentor_available'] = htmlspecialchars($this->user->mentorAvailable);
        $response['apprentice_available'] = htmlspecialchars($this->apprenticeAvailable);
        return $response; 
    }
}
 

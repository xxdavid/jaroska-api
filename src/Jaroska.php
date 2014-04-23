<?php

namespace Jaroska;

use Jaroska\Authentication\Authenticator;
use Jaroska\Grades;

class Jaroska
{
    /** @var Authenticator */
    private $authenticator;

    /** @var  string */
    private $lastHtml;


    /**
     * @param string $username
     * @param string $password
     */
    public function authenticate($username, $password)
    {
        $this->authenticator = new Authenticator();
        $this->authenticator->authenticate($username, $password);
    }


    /**
     * @param string $session
     */
    public function authenticateViaSession($session)
    {
        $this->authenticator = new Authenticator();
        $this->authenticator->setSession($session);
    }


    /**
     * @param string|null $html
     * @return Grades\Subject[]
     * @throws Authentication\Exception if user isn't authenticated
     */
    public function getGrades($html = null)
    {
        $gradesParser = new Grades\GradesParser();
        if (!$html) {
            if (isset($this->authenticator)) {
                $html =$gradesParser->fetch($this->authenticator);
            } else {
                throw new Authentication\Exception(
                    "Not authenticated. Call authenticate().",
                    Authentication\Exception::NOT_AUTHENTICATED
                );
            }
        }
        $this->lastHtml = $html;
        return $gradesParser->parse($html);
    }


    /**
     * @return string
     */
    public function getSession()
    {
        return $this->authenticator->getSession();
    }


    /**
     * @return string
     */
    public function getLastHtml()
    {
        return $this->lastHtml;
    }
}

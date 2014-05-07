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
     * 
     * @param \Jaroska\Parser $parser
     * @param string|null $html
     * @throws Authentication\Exception if user isn't authenticated
     */
    private function get(Parser $parser, $html)
    {
        if (!$html) {
            if (isset($this->authenticator)) {
                $html = $parser->fetch($this->authenticator);
            } else {
                throw new Authentication\Exception(
                    "Not authenticated. Call authenticate().",
                    Authentication\Exception::NOT_AUTHENTICATED
                );
            }
        }
        $this->lastHtml = $html;
        return $parser->parse($html); 
    }

    
    /**
     * @param string|null $html
     * @return Grades\Subject[]
     * @throws Authentication\Exception if user isn't authenticated
     */
    public function getGrades($html = null)
    {
        $gradesParser = new Grades\GradesParser;
        return $this->get($gradesParser, $html);
    }


    /**
     * @param string|null $html
     * @return array
     * @throws Authentication\Exception if user isn't authenticated
     */
    public function getNews($html = null)
    {
        $newsParser = new News\NewsParser();
        return $this->get($newsParser, $html);
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

<?php

namespace Jaroska;

use Jaroska\Authentication\Authenticator;
use Jaroska\Grades;

class Jaroska
{
    const TIMETABLE_FULL = 1;
    const TIMETABLE_EXCLUDE_PERIODS = 2;
    const TIMETABLE_MINIMAL = 3;

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
    private function get(Parser $parser, $html, $needsAuthentication)
    {
        if (!$html) {
            if ($needsAuthentication){
                if (isset($this->authenticator)) {
                    $html = $parser->fetch($this->authenticator);
                } else {
                    throw new Authentication\Exception(
                        "Not authenticated. Call authenticate().",
                        Authentication\Exception::NOT_AUTHENTICATED
                    );
                }
            } else {
                $html = $parser->fetch();
            }
        }
        $this->lastHtml = $html;
        return $parser->parse($html);
    }


    /**
     * @param string|null $html
     * @return Grades\Subject[]
     * @throws Authentication\Exception if user isn't authenticated
     * @auth
     */
    public function getGrades($html = null)
    {
        $gradesParser = new Grades\GradesParser;
        return $this->get($gradesParser, $html, true);
    }


    /**
     * @param string|null $html
     * @return array
     * @throws Authentication\Exception if user isn't authenticated
     * @auth
     */
    public function getNews($html = null)
    {
        $newsParser = new News\NewsParser();
        return $this->get($newsParser, $html, true);
    }


    /**
     *
     * @param string $class
     * @param string $html
     */
    public function getTimetableLinks($html = null)
    {
        $timetableLinksParser = new Timetable\TimetableLinksParser();
        return $this->get($timetableLinksParser, $html, false);
    }


    /**
     *
     * @param string $class
     * @param string $html
     */
    public function getTimetable($link = null, $mode = self::TIMETABLE_EXCLUDE_PERIODS, $html = null)
    {
        if (!$link == !$html){
            throw new \InvalidArgumentException('You have to call getTimetable() with either $class or $html argument.');
        }
        if ($mode > 3){
            throw new \InvalidArgumentException('Invalid mode.');
        }
        $timetableParser = new Timetable\TimetableParser();
        if (!$html) {
            $html = $timetableParser->fetch($link);
        }
        $this->lastHtml = $html;
        return $timetableParser->parse($html, $mode);
    }


    /**
     * @param string|null $html
     * @return array
     */
    public function getEmployeesList($html = null)
    {
        $employeesListParser = new Employees\EmployeesListParser();
        return $this->get($employeesListParser, $html, false);
    }


    /**
     * @param int $id
     * @param string|null $html
     * @return \Jaroska\Employees\Employee
     */
    public function getEmployeeInfo($id, $html = null)
    {
        if (!$id == !$html) {
            throw new \InvalidArgumentException('You have to call getEmployeeInfo() with either $id or $html argument.');
        }
        $employeeInfoParser = new Employees\EmployeeInfoParser();
        if (!$html) {
            $html = $employeeInfoParser->fetch($id);
        }
        $this->lastHtml = $html;
        return $employeeInfoParser->parse($html);
    }


    /**
     * @return string
     * @auth
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

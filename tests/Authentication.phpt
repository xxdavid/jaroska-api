<?php

/**
 * Test: Authentication test.
 *
 * @testCase
 */

namespace Jaroska\Tests;

use Tester;
use Tester\Assert;
use Tester\DomQuery;
use \Jaroska;
use \Jaroska\Authentication;

require __DIR__ . '/bootstrap.php';

class AuthenticationTest extends Tester\TestCase
{
    /* @var Jaroska\Jaroska */
    private $jaroska;


    public function setUp()
    {
        $this->jaroska = new Jaroska\Jaroska();
    }


    public function testPasswordAuthentication()
    {
        $configuration = include("config.php");

        $this->jaroska->authenticate($configuration["username"], $configuration["password"]);
        $session = $this->jaroska->getSession();
        $this->jaroska->getGrades();
        $html = $this->jaroska->getLastHtml();

        $dom = DomQuery::fromHtml($html);
        Assert::true($dom->has('#hlavni'));
        Assert::true($dom->has('.znamkyzpredmetu'));

        $this->sessionAuthentication($session, $html);
    }


    /**
     * @param string $session
     * @param string $html
     */
    private function sessionAuthentication($session, $html)
    {
        $this->jaroska->authenticateViaSession($session);
        $this->jaroska->getGrades();
        $gradesHtml = $this->jaroska->getLastHtml();

        Assert::same($gradesHtml, $html);
    }


    public function testNotAuthenticatedException()
    {
        Assert::exception(
            function () {
                $this->jaroska->getGrades();
            },
            'Jaroska\Authentication\Exception',
            "Not authenticated. Call authenticate().",
            Authentication\Exception::NOT_AUTHENTICATED
        );

    }


    public function testSessionExpiredException()
    {

        Assert::exception(function () {
            $this->jaroska->authenticateViaSession("SWAG!");
            $this->jaroska->getGrades();
        }, 'Jaroska\Authentication\Exception', null, Authentication\Exception::INVALID_SESSION);

    }


    public function testInvalidCredentials()
    {
        Assert::exception(function () {
            $this->jaroska->authenticate("xFry00", "Password1");
            $this->jaroska->getGrades();
        }, 'Jaroska\Authentication\Exception', null, Authentication\Exception::INVALID_CREDENTIALS);
    }
}

$testCase = new AuthenticationTest();
$testCase->run();

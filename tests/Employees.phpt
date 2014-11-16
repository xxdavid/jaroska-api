<?php
/**
* Test: Employees
*
* @testCase
*/

namespace Jaroska\Tests;

use Tester;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';


class EmployeesTest extends Tester\TestCase
{
    /** @var  \Jaroska\Jaroska */
    private $jaroska;

    public function setUp()
    {
        $this->jaroska = $jaroska = new \Jaroska\Jaroska();
    }


    public function testEmployeesList()
    {
        $employeesList = $this->jaroska->getEmployeesList();
        Assert::equal($employeesList['Profesoři']['Mgr. Ježek Viktor'], 1180);
    }

    public function testEmployeesListOffline()
    {
        $html = file_get_contents(__DIR__ . '/mock-sources/employees_list.htm');
        $employeesList = $this->jaroska->getEmployeesList($html);
        Assert::equal($employeesList['Ředitel školy']['RNDr. Herman Jiří, Ph.D.'], 1010);
    }


    public function testEmployeeInfo()
    {
        $info = $this->jaroska->getEmployeeInfo(1010);
        Assert::same('RNDr. Jiří Herman, Ph.D.', $info->name);
        Assert::same('herman@jaroska.cz', $info->email);
        Assert::same('545577371', $info->telephone);
        Assert::same([
            'ředitel školy',
            'aprobace: M-F',
            'výuka matematiky ve třídách zaměřených na matematiku',
            'výuka na PřF MU',
            'předseda Krajské komise matematické olympiády',
            'autorství učebnic',
            'člen redakční rady Programů gymnázia',
        ], $info->otherInfo);
        Assert::same('http://www.jaroska.cz/zamestnanci/herman.jpg', $info->image);
    }


    public function testEmployeeInfoOffline()
    {
        $html = file_get_contents(__DIR__ . '/mock-sources/employees_blaha.htm');
        $info = $this->jaroska->getEmployeeInfo(null, $html);
        Assert::same('Mgr. Marek Blaha', $info->name);
        Assert::same('marba@jaroska.cz', $info->email);
        Assert::same('545 321 282 kl. 35', $info->telephone);
        Assert::same([
            'aprobace: M-Inf',
            'ICT koordinátor',
            'předseda PK informatika - programování',
            'člen redakční rady Programů gymnázia',
        ], $info->otherInfo);
        Assert::same('http://www.inline-online.cz/', $info->website);
        Assert::same('http://www.jaroska.cz/zamestnanci/blaha.jpg', $info->image);
    }


    public function testInvalidArguments()
    {
        Assert::exception(function() {
            $this->jaroska->getEmployeeInfo(42, 'blahblablah');
        }, '\InvalidArgumentException');
    }
}

$testCase = new EmployeesTest;
$testCase->run();

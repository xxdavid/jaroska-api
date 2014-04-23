<?php
/**
* Test: Grades
*
* @testCase
*/

namespace Jaroska\Tests;

use Tester;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';


class GradesTest extends Tester\TestCase
{
    /** @var \Jaroska\Grades\Subject[] */
    private $subjects;


    public function setUp()
    {
        $jaroska = new \Jaroska\Jaroska();
        $html = file_get_contents('mock-sources/grades.htm');
        $this->subjects = $jaroska->getGrades($html);

    }


    public function testSubjects()
    {
        Assert::type("array", $this->subjects);
        Assert::type('Jaroska\Grades\Subject', $this->subjects['Matematika']);
    }


    public function testGrades()
    {
        Assert::type("array", $this->subjects["Matematika"]->grades);
        Assert::same(3, count($this->subjects["Matematika"]->grades));
        Assert::type('Jaroska\Grades\Grade', $this->subjects["Matematika"]->grades[1]);
        Assert::same('29b', $this->subjects["Matematika"]->grades[1]->grade);
        Assert::same('Soustavy - 30b', $this->subjects["Matematika"]->grades[1]->description);
        Assert::same(1395788400, $this->subjects["Matematika"]->grades[1]->date);
        Assert::same('2014-03-26', date('Y-m-d', $this->subjects["Matematika"]->grades[1]->date));

        Assert::type("array", $this->subjects["Fyzika"]->grades);
        Assert::type('Jaroska\Grades\Grade', $this->subjects["Fyzika"]->grades[0]);
        Assert::same(
            'Prověrka - Vrh svislý vzhůru, vrh vodorovný',
            $this->subjects["Fyzika"]->grades[0]->description
        );
    }


    public function testTeachersName()
    {
        Assert::type('string', $this->subjects['Český jazyk a literatura']->teacher->name);
        Assert::same('Edna Krabappel', $this->subjects['Český jazyk a literatura']->teacher->name);

        Assert::type('string', $this->subjects['Hudební výchova']->teacher->name);
        Assert::same('Dewey Largo', $this->subjects['Hudební výchova']->teacher->name);
    }


    public function testTeachersEmail()
    {
        Assert::type('string', $this->subjects['Anglický jazyk']->teacher->name);
        Assert::same('juniper@SpringfieldES.com', $this->subjects['Anglický jazyk']->teacher->email);

        Assert::type('string', $this->subjects['Informatika']->teacher->name);
        Assert::same('bergstrom@SpringfieldES.com', $this->subjects['Informatika']->teacher->email);
    }
}

$testCase = new GradesTest;
$testCase->run();

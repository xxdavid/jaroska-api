<?php
/**
* Test: Timetable
*
* @testCase
*/

namespace Jaroska\Tests;

use Tester;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';


class TimetableTest extends Tester\TestCase
{
    /** @var  \Jaroska\Jaroska */
    private $jaroska;

    public function setUp()
    {
        $this->jaroska = $jaroska = new \Jaroska\Jaroska();
    }


    public function testTimetableLinks()
    {
        $html = file_get_contents('mock-sources/timetable_menu.htm');
        $links = $this->jaroska->getTimetableLinks($html);
        Assert::type('array', $links);
        Assert::same('rozvrh_tr114.htm', $links['1.C']);
        Assert::same('rozvrh_tr131.htm', $links['2bg']);
    }


    public function testMinimal()
    {
        $html = file_get_contents('mock-sources/timetable.htm');
        $timetableData = $this->jaroska->getTimetable(null, \Jaroska\Jaroska::TIMETABLE_MINIMAL, $html);
        $lesson = $timetableData->week['čtvrtek'][4][2];
        Assert::same('NSed', $lesson->subject);
        Assert::same('německý jazyk', $timetableData->subjects[$lesson->subject]->name);
        Assert::same('Sed', $timetableData->subjects[$lesson->subject]->teacher);
        Assert::same('1B', $lesson->classroom);
        Assert::same('učebna 1.B', $timetableData->classrooms[$lesson->classroom]->name);
        Assert::same('10:45', $timetableData->periods[4]->start);
        Assert::same('11:30', $timetableData->periods[4]->end);
        Assert::same('20.9.2013', date('j.n.Y', $timetableData->lastUpdate));
    }


    public function testExcludePeriods()
    {
        $html = file_get_contents('mock-sources/timetable.htm');
        $timetableData = $this->jaroska->getTimetable(null, \Jaroska\Jaroska::TIMETABLE_EXCLUDE_PERIODS, $html);
        $lesson = $timetableData->week['úterý'][1][3];
        Assert::same('ruský jazyk', $lesson->subject->name);
        Assert::same('R', $lesson->subject->abbreviation);
        Assert::same('Bc. Michal Horák', $lesson->subject->teacher->name);
        Assert::same('Hor', $lesson->subject->teacher->abbreviation);
        Assert::same('eurojazykovka', $lesson->classroom->name);
        Assert::same('J5', $lesson->classroom->abbreviation);
        Assert::same('7:55', $timetableData->periods[1]->start);
        Assert::same('8:40', $timetableData->periods[1]->end);
        Assert::same('20.9.2013', date('j.n.Y', $timetableData->lastUpdate));
    }


    public function testFull()
    {
        $html = file_get_contents('mock-sources/timetable.htm');
        $timetableData = $this->jaroska->getTimetable(null, \Jaroska\Jaroska::TIMETABLE_FULL, $html);
        $lesson = $timetableData->week['středa'][7][0];
        Assert::same('zeměpis', $lesson->subject->name);
        Assert::same('Z', $lesson->subject->abbreviation);
        Assert::same('Mgr. Ivan Hlásenský', $lesson->subject->teacher->name);
        Assert::same('Hs', $lesson->subject->teacher->abbreviation);
        Assert::same('učebna 1.C', $lesson->classroom->name);
        Assert::same('1C', $lesson->classroom->abbreviation);
        Assert::same('13:50', $lesson->period->start);
        Assert::same('14:35', $lesson->period->end);
        Assert::same('20.9.2013', date('j.n.Y', $timetableData->lastUpdate));
    }
}

$testCase = new TimetableTest;
$testCase->run();

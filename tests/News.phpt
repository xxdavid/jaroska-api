<?php
/**
 * Test: Grades
 *
 * @testCase
 */

namespace Jaroska\Tests;

use Jaroska\News\Boards;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';


class GradesTest extends Tester\TestCase
{
    /** @var Boards */
    private $news;


    public function setUp()
    {
        $jaroska = new \Jaroska\Jaroska();
        $html = file_get_contents('mock-sources/news.htm');
        $this->news = $jaroska->getNews($html);

    }

    public function testBoards()
    {
        Assert::type('array', $this->news->global);
        Assert::type('array', $this->news->class);
    }


    public function testNewsId()
    {
        Assert::same(2693, $this->news->global[0]->id);
        Assert::same(2737, $this->news->class[0]->id);
    }
    
    
    public function testNewsTitle()
    {
        Assert::same("Výměnný pobyt Brno - Darmstadt", $this->news->global[1]->title);
    }


    public function testNewsText()
    {
        Assert::same(
            'Pro všechny zájemce o zkoušky Cambridge English se nabízí nové možnosti pretestingu. Více čtěte na nástěnce v J1 v 5. poschodí.',
            $this->news->global[8]->text
        );
    }


    public function testTimestamp()
    {
        Assert::same(1409819485, $this->news->global[2]->timestamp);
        Assert::same('04.09.2014 10:31:25', date('d.m.Y H:i:s', $this->news->global[2]->timestamp));
    }
    
    public function testAuthor()
    {
        Assert::same('Ing. Viera Hájková', $this->news->global[3]->author);
    }
}

$testCase = new GradesTest;
$testCase->run();

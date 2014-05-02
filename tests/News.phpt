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
        Assert::same(2526, $this->news->global[2]->id);
        Assert::same(2586, $this->news->class[0]->id);
    }
    
    
    public function testNewsTitle()
    {
        Assert::same("Matematika II 4.BC", $this->news->global[3]->title);
    }


    public function testNewsText()
    {
        Assert::same(
            'známky po trojích opravkách a doplňujících písemkách aktuální k dnešnímu dni.
Celková známka:
1) nematuranti z M (5 lidí) - průměr všech procent s hranicemi známek 90/80/65/50
2) maturanti z M (29 lidí) - musí mít splněno u sbírky maturitních příkladů a devětkrár ANO u devíti "státně-maturitních" minitestů, pokud je splněno, pak známka je průměrem z písemek viz výše, v opačném případě neklasifikován a maturita v září
Termín - do klasifikační porady 8.4.2014.',
            $this->news->global[3]->text
        );
    }


    public function testTimestamp()
    {
        Assert::same(1397820215, $this->news->global[9]->timestamp);
        Assert::same('18.04.2014 13:23:35', date('d.m.Y H:i:s', $this->news->global[9]->timestamp));
    }
    
    public function tesAuthor()
    {
        Assert::same('Mgr. Viktor Ježek', $this->news->global[9]->author);
    }
}

$testCase = new GradesTest;
$testCase->run();

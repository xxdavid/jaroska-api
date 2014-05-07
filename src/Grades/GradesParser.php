<?php

namespace Jaroska\Grades;

use Jaroska\Parser;
use Symfony\Component\DomCrawler\Crawler;

class GradesParser extends Parser
{
    protected static $url = 'https://is.jaroska.cz/index.php?akce=650';


    /**
     * @param string $html
     * @return Subject[]
     */
    public function parse($html)
    {
        $crawler = new Crawler($html);
        $h3s = $crawler->filter('h3');
        $gradesInSubjectElements = $crawler->filter('.znamkyzpredmetu');
        $subjects = array();
        for ($i = 0; $i <= ((count($h3s) / 2) - 1); $i++) {
            $gradesElement = $gradesInSubjectElements->eq($i);
            $subjectName = $this->parseSubjectName($h3s->eq($i * 2)->text());
            $teachersInfo = $h3s->eq(($i * 2) + 1);
            $subject = new Subject();
            $subject->teacher = new Teacher();
            $subject->teacher->name = $this->parseTeachersName($teachersInfo);
            $subject->teacher->email = $this->parseTeachersEmail($teachersInfo);
            $subject->grades = $this->parseGrades($gradesElement);
            $subjects[$subjectName] = $subject;
        }
        return $subjects;
    }


    /**
     * @param Crawler $crawler
     * @return Grade[]
     */
    private function parseGrades(Crawler $crawler)
    {
        $grades = $crawler->filter("span")
            ->each(function (Crawler $gradeElement) {
            $grade = new Grade;
            $grade->grade = $gradeElement->text();
            $title = $gradeElement->attr('title');
            $parts = explode(' - ', $title, 2);
            $grade->date = strtotime($parts[0]);
            $grade->description = $parts[1];
            $grade->description = str_replace(
                " - komentář učitele neuveden",
                '',
                $grade->description
            );
            $grade->description = trim($grade->description);
            return $grade;
            });
        return $grades;
    }


    /**
     * @param string $str
     * @return string
     */
    private function parseSubjectName($str)
    {
        return str_replace('Předmět: ', '', $str);
    }


    /**
     * @param Crawler $crawler
     * @return string
     */
    private function parseTeachersName(Crawler $crawler)
    {
        $str = $crawler->text();
        $str = str_replace('Vyučující: ', '', $str);
        $str = str_replace('(email)', '', $str);
        return trim($str);
    }


    /**
     * @param Crawler $crawler
     * @return string
     */
    private function parseTeachersEmail(Crawler $crawler)
    {
        $str = $crawler->filter('a')->attr("href");
        return str_replace('mailto:', '', $str);
    }
}

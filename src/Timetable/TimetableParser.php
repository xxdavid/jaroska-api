<?php

namespace Jaroska\Timetable;

use Symfony\Component\DomCrawler\Crawler;
use \Jaroska\Networking\Request;
use \Jaroska\Jaroska;

class TimetableParser
{
    const BASE_URL = 'http://www.jaroska.cz/files/rozvrhy';


    public function fetch($link)
    {

        $request = new Request(
            self::BASE_URL . '/' . $link,
            null,
            Request::GET
        );
        return $request->getContent();
    }


    /**
     * @param string $html
     * @param int $mode
     * @return Timetable
     */
    public function parse($html, $mode)
    {
        $crawler = new Crawler($html);
        $periods = $this->parsePeriods($crawler);
        $lastUpdate = $this->parseUpdateDate($crawler);
        $timetable = array();
        if ($mode == Jaroska::TIMETABLE_MINIMAL) {
            $subjects = array();
            $teachers = array();
            $classrooms = array();
        }
        $cells = $crawler->filter('tr')->eq(2)->filter('table')->first()->filter('td');
        for ($i2 = 3; $i2 <= ((count($cells) - 1)); $i2++) {
            $cell = $cells->eq($i2);
            $fonts = $cell->filter('font');
            if (count($fonts)) {
                preg_match('#\*\s(.+)\s:\s(\d)(?:-(\d))?#', $cell->attr('title'), $matches);
                $day = $matches[1];
                $startPeriod = (int)$matches[2];
                $endPeriod = (int)isset($matches[3]) ? $matches[3] : $matches[2];
                $lesson = new Lesson();
                if ($mode == Jaroska::TIMETABLE_MINIMAL) {
                    $subjectInfo = $this->parseFontTitle($fonts->eq(0)->attr('title'));
                    $teacherInfo = $this->parseFontTitle($fonts->eq(1)->attr('title'));
                    $subjectId = $subjectInfo['abbreviation'] . $teacherInfo['abbreviation'];
                    if (!isset($subjects[$subjectId])) {
                        $subjects[$subjectId] = new Subject();
                        $this->extractToObject($subjectInfo, $subjects[$subjectId]);

                        if (!isset($teachers[$teacherInfo['abbreviation']])) {
                            $teachers[$teacherInfo['abbreviation']] = new Teacher();
                            $this->extractToObject($teacherInfo, $teachers[$teacherInfo['abbreviation']]);
                        }
                        $subjects[$subjectId]->teacher = $teacherInfo['abbreviation'];
                    }
                    $classroomInfo = $this->parseFontTitle($fonts->eq(2)->attr('title'));
                    if (!isset($classrooms[$classroomInfo['abbreviation']])) {
                        $classrooms[$classroomInfo['abbreviation']] = new Classroom();
                        $this->extractToObject($classroomInfo, $classrooms[$classroomInfo['abbreviation']]);
                    }

                    $lesson->subject = $subjectId;
                    $lesson->classroom = $classroomInfo['abbreviation'];
                } else {
                    $lesson->subject = new Subject();
                    $this->extractToObject($this->parseFontTitle($fonts->eq(0)->attr('title')), $lesson->subject);
                    $lesson->subject->teacher = new Teacher();
                    $this->extractToObject($this->parseFontTitle($fonts->eq(1)->attr('title')), $lesson->subject->teacher);
                    $lesson->classroom = new Classroom();
                    $this->extractToObject($this->parseFontTitle($fonts->eq(2)->attr('title')), $lesson->classroom);
                }
                for ($i3 = $startPeriod; $i3 <= $endPeriod; $i3++) {
                    if ($mode == Jaroska::TIMETABLE_FULL) {
                        $lesson->period = new Period();
                        $lesson->period = $periods[$i3];
                    }
                    if (isset($timetable[$day])) {
                        if (isset($timetable[$day][$i3])) {
                            $timetable[$day][$i3][count($timetable[$day][$i3])] = clone $lesson;
                        } else {
                            $timetable[$day][$i3][0] = clone $lesson;
                        }
                    } else {
                        $timetable[$day][$i3][0] = clone $lesson;
                    }
                }
            }
        }

        $return = new Timetable();
        if ($mode != Jaroska::TIMETABLE_FULL) {
            $return->periods = $periods;
        }
        if ($mode == Jaroska::TIMETABLE_MINIMAL) {
            $return->subjects = $subjects;
            $return->teachers = $teachers;
            $return->classrooms = $classrooms;
        }
        $return->week = $timetable;
        $return->lastUpdate = $lastUpdate;
        return $return;
    }


    /**
     * @param Crawler $crawler
     * @return \Jaroska\Timetable\Period[]
     */
    private function parsePeriods(Crawler $crawler)
    {
        return $crawler->filter('tr.HlavickaCas > td')->each(function (Crawler $periodElement, $i) {
            $string = trim($periodElement->text());
            $matches = array();
            preg_match('#(\d+:\d+) -  ?(\d+:\d+)#', $string, $matches);
            $period = new Period();
            $period->start = $matches[1];
            $period->end = $matches[2];
            return $period;
        });
    }


    /**
     * @param string $string
     * @return array
     */
    private function parseFontTitle($string)
    {
        preg_match('#(.+) = (.+)#', $string, $matches);
        return [
            'name' => trim($matches[2]),
            'abbreviation' => $matches[1]
        ];
    }


    /**
     * @param array $array
     * @param $object
     */
    private function extractToObject(array $array, &$object)
    {
        foreach ($array as $key => $value) {
            $object->$key = $value;
        }
    }


    /***/
    private function parseUpdateDate(Crawler $crawler)
    {
        return strtotime($crawler->filter('tr.Info > td')->first()->text());
    }
}

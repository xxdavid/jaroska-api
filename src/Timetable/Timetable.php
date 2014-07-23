<?php

namespace Jaroska\Timetable;

/**
 * @property Period[] $periods
 * @property Subject[] $subjects
 * @property Teacher[] $teachers
 * @property Classroom[] $classrooms
 */
class Timetable
{
    /** @var array */
    public $week;

    /** @var \DateTime */
    public $lastUpdate;
}

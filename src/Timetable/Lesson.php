<?php

namespace Jaroska\Timetable;

/**
 * @property Period $period
 */
class Lesson
{
    /** @var Subject|string Subject object ('full' and 'exclude_periods' mode) or abbreviation of the subject ('minimal' mode) */
    public $subject;

    /** @var Classroom|string Classroom object ('full' and 'exclude_periods' mode) or abbreviation of the classroom ('minimal' mode) */
    public $classroom;
}

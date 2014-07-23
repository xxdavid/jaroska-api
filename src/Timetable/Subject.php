<?php

namespace Jaroska\Timetable;

class Subject
{
    /** @var string */
    public $name;

    /** @var string */
    public $abbreviation;

    /** @var Teacher|string Teacher object ('full' and 'exclude_periods' mode) or abbreviation of the teacher ('minimal' mode) */
    public $teacher;
}

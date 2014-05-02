<?php

namespace Jaroska\News;

class NewsItem
{
    /** @var int */
    public $id;

    /** @var string */
    public $title;

    /** @var string*/
    public $text;

    /** @var int Unix timestamp */
    public $timestamp;

    /** @var string Author's name; */
    public $author;

}

<?php

namespace Jaroska\Employees;

use Jaroska\Parser;
use Symfony\Component\DomCrawler\Crawler;

class EmployeesListParser extends Parser
{
    protected static $url = 'http://www.jaroska.cz/node/12';

    /**
     * @param string $html
     * @return array
     */
    function parse($html)
    {
        $crawler = new Crawler($html);
        $children = $crawler->filter('.content')->first()->children();

        $employees = [];
        $heading = '';

        foreach($children as $child) {
            $tagName = $child->tagName;
            switch ($tagName) {
                case 'br':
                    continue;
                    break;
                case 'h3';
                    $heading = $child->nodeValue;
                    break;
                case 'a':
                    $employees[$heading][$child->nodeValue] = $this->parseId($child->getAttribute('href'));
            }
        }
        return $employees;
    }

    /**
     * @param string $href
     * @return string
     */
    public function parseId($href)
    {
        preg_match('#\d{2}\?ID=(\d{4})#', $href, $matches);
        return (int) $matches[1];
    }
}

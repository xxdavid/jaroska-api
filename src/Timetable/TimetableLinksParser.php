<?php

namespace Jaroska\Timetable;

use Jaroska\Parser;
use Symfony\Component\DomCrawler\Crawler;

class TimetableLinksParser extends Parser
{
    protected static $url = 'http://www.jaroska.cz/files/rozvrhy/rozvrh_tr_menu.htm';


    /**
     * @param string $html
     * @return array
     */
    public function parse($html)
    {
        $crawler = new Crawler($html);
        $options = $crawler->filter('select[name=vyber] > option');
        $links = array();
        for ($i = 0; $i <= ((count($options) - 1)); $i++) {
            $option = $options->eq($i);
            $class = $option->text();
            $class = substr($class, 0, ((strlen($class) / 2) - 2));
            $links[$class] = $option->attr('value');
        }
        return $links;
    }
}

<?php

namespace Jaroska\Employees;

use Symfony\Component\DomCrawler\Crawler;
use Jaroska\Networking\Request;

class EmployeeInfoParser
{
    const BASE_URL = 'http://www.jaroska.cz/node/48?ID=';


    /**
     * @param int $id
     * @return string
     */
    public function fetch($id)
    {

        $request = new Request(
            self::BASE_URL . $id,
            null,
            Request::GET
        );
        return $request->getContent();
    }


    /**
     * @param $html
     * @return \Jaroska\Employees\Employee
     */
    public function parse($html)
    {
        $crawler = new Crawler($html);
        $content = $crawler->filter('.content')->first();
        $employee = new Employee();
        $employee->name = $content->filter('h1')->first()->text();
        $list = $content->filter('ul > li');
        $employee->otherInfo = $list->each(function(Crawler $item){
            return $item->text();
        });
        $node = $content->filter('ul')->getNode(0);
        while ($node->nextSibling) {
            $node = $node->nextSibling;
            if (strpos($node->nodeValue, 'E-mail')) {
                $employee->email = $this->parseEmail($node->nodeValue);
            }
            if (strpos($node->nodeValue, 'Telefon')) {
                $employee->telephone = $this->parseTelephone($node->nodeValue);
            }
            if (strpos($node->nodeValue, 'Web')) {
                $employee->website = $node->nextSibling->attributes->getNamedItem('href')->nodeValue;
            }

        }
        $image = $content->filter('img');
        if (count($image)) {
            $employee->image = $image->first()->attr('src');
        }
        return $employee;
    }


    /**
     * @param string $string
     * @return string
     */
    public function parseEmail($string)
    {
        $string = preg_replace('#E-mail: #', '', $string);
        $string = preg_replace('# \[zavinac\] #', '@', $string);
        $string = trim($string);
        return $string;
    }


    /**
     * @param string $string
     * @return string
     */
    public function parseTelephone($string)
    {
        $string = preg_replace('#Telefon: #', '', $string);
        $string = trim($string);
        return $string;
    }
}
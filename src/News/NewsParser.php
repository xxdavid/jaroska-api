<?php

namespace Jaroska\News;

use Jaroska\Parser;
use Symfony\Component\DomCrawler\Crawler;

class NewsParser extends Parser
{
    protected static $url = 'https://is.jaroska.cz/index.php';


    /**
     * @param string $html
     * @return Boards
     */
    public function parse($html)
    {
        $crawler = new Crawler($html);
        $boards = new Boards();
        $boardsElement = $crawler->filter('.nastenka');
        for ($i = 0; $i <= 1; $i++) {
            $news = $boardsElement->eq($i)->filter('.nanastence')
                ->each(function (Crawler $newsItemElement) {
                    $newsItem = new NewsItem;
                    $newsItem->id = $this->parseId($newsItemElement);
                    $newsItem->title = $this->parseTitle($newsItemElement);
                    $newsItem->text = $this->parseText($newsItemElement);
                    $newsItem->author = $this->parseAuthor($newsItemElement);
                    $newsItem->timestamp = $this->parseDate($newsItemElement);
                    return $newsItem;
                });
            $news = array_reverse($news);
            if ($i === 0) {
                $boards->global = $news;
            } else {
                $boards->class = $news;
            }
        }
        return $boards;
    }


    /**
     * @param Crawler $crawler
     * @return int
     */
    private function parseId(Crawler $crawler)
    {
        $link = $crawler->filter('.prikazynastenky')->filter('a')->attr('href');
        parse_str($link, $params);
        $id = (int)$params['id'];
        return $id;

    }


    /**
     * @param Crawler $crawler
     * @return string
     */
    private function parseTitle(Crawler $crawler)
    {
        $str = $crawler->filter('.nadpis')->text();
        $str = $this->unescapeQuotationMarks($str);
        return $str;
    }


    /**
     * @todo Parse only <br> for line breaks
     * @param Crawler $crawler
     * @return string
     */
    private function parseText(Crawler $crawler)
    {
        $str = $crawler->filter('.textik')->text(); //textik...
        $str = $this->unescapeQuotationMarks($str);
        $str = str_replace("\r\n\r\n", "\r\n", $str);
        $str = trim($str);
        return $str;
    }


    /**
     * @param Crawler $crawler
     * @return string
     */
    private function parseAuthor(Crawler $crawler)
    {
        $str = $crawler->filter('.autor')->text();
        $str = str_replace('Autor:', '', $str);
        $str = preg_replace('# , [0-4]\...?#', '', $str);
        $str = preg_replace('# - .*#', '', $str);
        $str = trim($str);
        return $str;
    }


    /**
     * @param Crawler $crawler
     * @return int Unix timestamp
     */
    private function parseDate(Crawler $crawler)
    {
        $str = $crawler->filter('.autor')->text();
        $str = preg_replace('#.*Datum: #', '', $str);
        $str = trim($str);
        $timestamp = strtotime($str);
        return $timestamp;
    }


    /**
     * @param string $str
     * @return string
     */
    private function unescapeQuotationMarks($str)
    {
        return preg_replace('#\\\+"#', '"', $str);
    }
}

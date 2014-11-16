<?php
namespace Jaroska\Networking;

class Request
{
    const GET = 'GET';
    const POST = 'POST';

    /* @var string */
    private $content;

    /* @var string */
    private $rawResponseHeader;

    /* @var int */
    private $httpCode;

    /* @var string */
    private $effectiveUrl;

    /* @var string */
    private $cookies;

    /* @var int */
    private $redirectCount;


    /**
     * @param string $url
     * @param array|null $args
     * @param string $method
     * @param string|null $cookies
     * @param bool $followLocation
     * @throws \Jaroska\Networking\Exception
     */
    public function __construct(
        $url,
        $args = null,
        $method = self::GET,
        $cookies = null,
        $followLocation = false
    ) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEFILE, "");
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/StartComCertificationAuthority.crt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($args) {
            $implodedParams = http_build_query($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $implodedParams);
        }

        if ($method === self::POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $response = curl_exec($ch);
        if ($response == false) {
            throw new Exception("cURL error: " . curl_error($ch), curl_errno($ch));
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->content = substr($response, $header_size);
        $this->rawResponseHeader = substr($response, 0, $header_size);
        $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $this->redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        $this->cookies = $this->parseCookiesFromHeader($this->rawResponseHeader);
        curl_close($ch);
    }


    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }


    /**
     * @return string
     */
    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }


    /**
     * @return string
     */
    public function getEffectiveUrlWithoutQueryString()
    {
        return $this->removeQueryStringFromUrl($this->effectiveUrl);
    }


    /**
     * @return string
     */
    public function getCookies()
    {
        return $this->cookies;
    }


    /**
     * @param string $header Response Header
     * @return string|null
     */
    private function parseCookiesFromHeader($header)
    {
        $success = preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $m);
        $cookie = $success ? $m[1] : null;
        return $cookie;
    }


    /**
     * @return int
     */
    public function getRedirectCount()
    {
        return $this->redirectCount;
    }


    /**
     * @return string
     */
    public function getRawResponseHeader()
    {
        return $this->rawResponseHeader;
    }


    /**
     * @param string $url
     * @return string
     */
    private function removeQueryStringFromUrl($url)
    {
        $urlParts = explode("?", $url);
        return $urlParts[0];
    }
}

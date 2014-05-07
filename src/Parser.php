<?php

namespace Jaroska;

use Jaroska\Networking\Request;
use Jaroska\Authentication\Authenticator;

abstract class Parser
{
    /**
     * @var string
     */
    protected static $url;
    
    
    /**
     * @param Authenticator $authenticator
     * @return string
     */
    public function fetch(Authenticator $authenticator)
    {
        $request = new Request(
            static::$url,
            null,
            Request::GET,
            $authenticator->getSession()
        ); 
        $authenticator->validateAuthentication($request);
        return $request->getContent();
    }
    
    
    /**
     * @param string $html
     */
    abstract public function parse($html);
}

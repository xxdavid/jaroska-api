<?php

namespace Jaroska\Authentication;

use Jaroska\Networking\Request;

class Authenticator
{
    const AUTH_TYPE_CREDENTIALS = 0;
    const AUTH_TYPE_SESSION = 1;

    const LOGIN_URL = 'https://is.jaroska.cz/login.php';
    const INDEX_URL = 'https://is.jaroska.cz/index.php';

    /** @var string */
    private $session;

    /** @var string */
    private $authenticationType;

    /** @var boolean */
    private $authenticationSucceeded;


    /**
     * @param string $username
     * @param string $password
     */
    public function authenticate($username, $password)
    {
        $params = [];
        $params['formUsername'] = $username;
        $params['formPassword'] = $password;
        $request = new Request(self::LOGIN_URL, $params, Request::POST, null, false);
        $this->session = $request->getCookies();
        $this->authenticationType = self::AUTH_TYPE_CREDENTIALS;

    }


    /**
     * @param Request $request
     * @throws Exception
     */
    public function validateAuthentication(Request $request)
    {
        if ($request->getEffectiveUrlWithoutQueryString() === self::INDEX_URL) {
            if (strpos($request->getContent(), 'Nesprávné jméno nebo heslo!') !== false) {
                throw new Exception('Invalid credentials.', Exception::INVALID_CREDENTIALS);

            } elseif (strpos($request->getContent(), 'Probíhá údržba systému!') !== false) {
                throw new Exception('Information system maintenance. Please try again later.', Exception::MAINTENANCE);

            } elseif ($this->authenticationType === self::AUTH_TYPE_SESSION and
                strpos($request->getContent(), '<div class="loginpageinfo">') !== false
            ) {
                throw new Exception(
                    'Invalid session. Hint: 24 minutes after last request session expires.',
                    Exception::INVALID_SESSION
                );
            }
        }

        $this->authenticationSucceeded = true;

    }


    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }


    /**
     * @param string $session
     */
    public function setSession($session)
    {
        $this->session = $session;
        $this->authenticationType = self::AUTH_TYPE_SESSION;
    }


    /**
     * @return int
     */
    public function getAuthenticationType()
    {
        return $this->authenticationType;
    }
}

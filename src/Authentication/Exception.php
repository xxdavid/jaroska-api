<?php

namespace Jaroska\Authentication;


class Exception extends \Exception
{
    const NOT_AUTHENTICATED = 1;
    const INVALID_CREDENTIALS = 2;
    const INVALID_SESSION = 3;
    const MAINTENANCE = 4;
}

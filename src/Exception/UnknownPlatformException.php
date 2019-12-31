<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Exception;

use Throwable;

class UnknownPlatformException extends \Exception
{
    public function __construct($message = 'unknown_platform', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

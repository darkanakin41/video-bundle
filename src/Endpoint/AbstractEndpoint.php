<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Endpoint;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class AbstractEndpoint
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;

    public function __construct(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    abstract protected function initialize();

    protected function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }
}

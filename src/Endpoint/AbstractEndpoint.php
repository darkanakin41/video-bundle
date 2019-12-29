<?php

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */

namespace Darkanakin41\VideoBundle\Endpoint;

use Darkanakin41\VideoBundle\DependencyInjection\Darkanakin41VideoExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractEndpoint
{
    /**
     * @var array
     */
    private $config;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->config = $parameterBag->get(Darkanakin41VideoExtension::CONFIG_KEY);
        $this->initialize();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    abstract protected function initialize();
}

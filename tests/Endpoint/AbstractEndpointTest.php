<?php


namespace Darkanakin41\VideoBundle\Tests\Endpoint;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractEndpointTest extends WebTestCase
{
    abstract protected function getEndpoint();
}

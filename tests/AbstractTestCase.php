<?php

namespace Darkanakin41\VideoBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractTestCase extends WebTestCase
{
    protected static $client;

    /**
     * Retrieve the Registry for doctrine
     * @return Registry
     */
    public function getDoctrine()
    {
        /** @var Registry $doctrine */
        $doctrine = self::$container->get("doctrine");
        return $doctrine;
    }

    protected function setUp(): void
    {
        $this->initClient();
        $this->initDatabase();
    }

    protected function initClient()
    {
        static::$client = static::createClient();
    }

    /**
     * It ensures that the database contains the original fixtures of the
     * application. This way tests can modify its contents safely without
     * interfering with subsequent tests.
     */
    protected function initDatabase()
    {
        $buildDir = __DIR__.'/../build';
        $originalDbPath = $buildDir.'/original_test.db';
        $targetDbPath = $buildDir.'/test.db';
        if (!file_exists($originalDbPath)) {
            throw new \RuntimeException(sprintf("The fixtures file used for the tests (%s) doesn't exist. This means that the execution of the bootstrap.php script that generates that file failed. Open %s/bootstrap.php and replace `NullOutput as ConsoleOutput` by `ConsoleOutput` to see the actual errors in the console.", $originalDbPath, realpath(__DIR__.'/..')));
        }
        copy($originalDbPath, $targetDbPath);
    }
}

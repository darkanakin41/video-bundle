<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/*
 * This file is part of the Darkanakin41VideoBundle package.
 */
if (!($autoload = @include __DIR__.'/../vendor/autoload.php')) {
    echo <<<'EOT'
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT;
    exit(1);
}


AnnotationRegistry::registerLoader(function ($class) use ($autoload) {
    $autoload->loadClass($class);

    return class_exists($class, false);
});

// Test Setup: remove all the contents in the build/ directory
// (PHP doesn't allow to delete directories unless they are empty)
if (is_dir($buildDir = __DIR__.'/../build')) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($buildDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $fileinfo->isDir() ? rmdir($fileinfo->getRealPath()) : unlink($fileinfo->getRealPath());
    }
}

include __DIR__.'/Fixtures/App/AppKernel.php';

$application = new Application(new AppKernel('test', true));
$application->setAutoExit(false);

$input = new ArrayInput(['command' => 'doctrine:database:create']);
$application->run($input, new ConsoleOutput());

// Create database schema
$input = new ArrayInput(['command' => 'doctrine:schema:create']);
$application->run($input, new ConsoleOutput());

// Load fixtures of the AppTestBundle
$input = new ArrayInput(['command' => 'doctrine:fixtures:load', '--no-interaction' => true, '--append' => false]);
$application->run($input, new ConsoleOutput());

// Make a copy of the original SQLite database to use the same unmodified database in every test
copy($buildDir.'/test.db', $buildDir.'/original_test.db');
unset($input, $application);

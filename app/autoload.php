<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;



/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->add('Ideup', __DIR__.'/../vendor/bundles');
$loader->add('Knp\\Component', __DIR__.'/../vendor/knp-components/src');
$loader->add('Knp\\Bundle', __DIR__.'/../vendor/bundles');
$loader->add('Zend_', __DIR__.'/../vendor/zend/lib');
//$loader->add('Zend_', __DIR__.'/../vendor/zend/lib');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
AnnotationDriver::registerAnnotationClasses();

return $loader;

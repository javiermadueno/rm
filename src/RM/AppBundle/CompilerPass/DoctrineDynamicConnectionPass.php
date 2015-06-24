<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/06/2015
 * Time: 10:02
 */

namespace RM\AppBundle\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineDynamicConnectionPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        try {
            if (!$container->has('security.token_storage')) {
                return;
            }

            $cliente = $_SESSION['connection'];

            $connections = $container->getExtensionConfig('doctrine')[0];

            if (!array_key_exists($cliente, $connections['dbal']['connections'])) {
                return;
            }

            $connections['orm']['entity_managers']['defaul']['connection'] = $cliente;


        } catch (\Exception $e) {
            return;
        }


    }
} 
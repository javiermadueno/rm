<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/04/2015
 * Time: 12:51
 */

namespace RM\PlantillaBundle\DomainManager;

use Doctrine\ORM\EntityManager;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class AbstractManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|EntityManager
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param DoctrineManager          $manager
     * @param EventDispatcherInterface $dispatcher
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager, EventDispatcherInterface $dispatcher)
    {
        try {
            $this->em = $manager->getManager();
        } catch (\Exception $e) {
            return;
        }
        $this->dispatcher = $dispatcher;
    }
} 
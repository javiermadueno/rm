<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/04/2015
 * Time: 12:51
 */

namespace RM\PlantillaBundle\DomainManager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class AbstractManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param ManagerRegistry          $doctrine
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ManagerRegistry $doctrine, EventDispatcherInterface $dispatcher)
    {
        if (!isset($_SESSION['connection'])) {
            return;
        }

        $this->em = $doctrine->getManager($_SESSION['connection']);
        $this->dispatcher = $dispatcher;
    }
} 
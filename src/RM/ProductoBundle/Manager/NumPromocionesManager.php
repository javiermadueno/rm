<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 25/06/2015
 * Time: 11:17
 */

namespace RM\ProductoBundle\Manager;


use Doctrine\Common\Persistence\ObjectManager;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NumPromocionesManager
{
    private $em;

    private $dispatcher;

    public function __construct(DoctrineManager $manager, EventDispatcherInterface $dispatcher)
    {
        $this->em = $manager->getManager();
        $this->dispatcher = $dispatcher;
    }

    public function remove(NumPromociones $numPromocion)
    {
        $numPromocion->setEstado(-1);
        $this->save($numPromocion);
    }

    public function save(NumPromociones $numPromocion)
    {
        $this->em->persist($numPromocion);
        $this->em->flush($numPromocion);
    }

} 
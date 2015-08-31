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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NumPromocionesManager
{
    private $em;

    private $dispatcher;

    private $validator;

    public function __construct(DoctrineManager $manager, EventDispatcherInterface $dispatcher, ValidatorInterface $validator)
    {
        $this->em         = $manager->getManager();
        $this->dispatcher = $dispatcher;
        $this->validator  = $validator;
    }

    public function remove(NumPromociones $numPromocion)
    {
        $this->em->remove($numPromocion);
        $this->em->flush($numPromocion);
    }

    public function save(NumPromociones $numPromocion)
    {
        $this->em->persist($numPromocion);
        $this->em->flush($numPromocion);
    }

    public function persist(NumPromociones $numPromocion)
    {
        $errors = $this->validator->validate($numPromocion);

        if(count($errors) > 0) {
            throw new \InvalidArgumentException('La num_promocion no es válida.' . (string) $errors);
        }
        $this->em->persist($numPromocion);
    }

    public function flush()
    {
        $this->em->flush();
    }

} 
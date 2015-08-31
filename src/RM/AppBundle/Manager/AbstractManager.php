<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/06/2015
 * Time: 14:13
 */

namespace RM\AppBundle\Manager;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractManager
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param DoctrineManager          $manager
     * @param EventDispatcherInterface $dispatcher
     * @param ValidatorInterface       $validator
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager, EventDispatcherInterface $dispatcher, ValidatorInterface $validator )
    {
        $this->em         = $manager->getManager();
        $this->dispatcher = $dispatcher;
        $this->validator  = $validator;

    }

} 
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 20/04/2015
 * Time: 12:25
 */

namespace RM\ComunicacionBundle\DomainManager;

use Doctrine\ORM\EntityManager;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\ComunicacionRepository;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Event\PlantillaEvent;
use RM\PlantillaBundle\Event\PlantillaEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ComunicacionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ComunicacionRepository
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(DoctrineManager $doctrine, EventDispatcherInterface $dispatcher, ValidatorInterface $validator)
    {
        $this->em = $doctrine->getManager();
        $this->repository = $this->em->getRespository('RMComunicacionBundle:Comunicacion');
        $this->dispatcher = $dispatcher;
        $this->validator = $validator;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $comunicacion = $this->repository->findById($id);

        return $comunicacion;
    }

    /**
     * @param Comunicacion $comunicacion
     * @throws \Exception
     */
    public function save(Comunicacion $comunicacion)
    {
        $errors = $this->validator->validate($comunicacion);
        if (!0 === count($errors)) {
            throw new \Exception(sprintf(
                    'La comunicación no es válida y no se ha podido guardar'
                ));
        }
        $this->em->persist($comunicacion);
        $this->em->flush();
    }

    public function importaPlantilla(Comunicacion $comunicacion, Plantilla $plantilla)
    {
        $old = $comunicacion->getPlantilla();

        $this->dispatcher->dispatch(PlantillaEvents::ELIMINAR_PLANTILLA, new PlantillaEvent($old));

        $comunicacion
            ->setPlantilla($plantilla);

        $this->save($comunicacion);
    }



} 
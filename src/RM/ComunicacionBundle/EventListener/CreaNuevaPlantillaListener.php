<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 09/02/2015
 * Time: 11:42
 */

namespace RM\ComunicacionBundle\EventListener;


use Doctrine\Common\Persistence\ManagerRegistry;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Estado;
use RM\ComunicacionBundle\Event\ComunicacionEvent;
use RM\ComunicacionBundle\Event\ComunicacionEvents;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreaNuevaPlantillaListener implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = isset($_SESSION['connection']) ? $doctrine->getManager($_SESSION['connection']) : null;
    }

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [ComunicacionEvents::NUEVA_COMUNICACION => 'onNuevaComunicacion'];
    }

    /**
     * Crea una plantilla nueva para la comunicación
     *
     * @param ComunicacionEvent $event
     *
     * @return bool
     */
    public function onNuevaComunicacion(ComunicacionEvent $event)
    {
        $comunicacion = $event->getComunicacion();

        if (!$comunicacion instanceof Comunicacion) {
            return false;
        }

        $plantilla = new Plantilla();
        $plantilla
            ->setEstado(Estado::ACTIVO)
            ->setCanal($comunicacion->getIdCanal());

        $this->em->persist($plantilla);

        $comunicacion->setPlantilla($plantilla);
        $this->em->persist($comunicacion);

        $this->em->flush();

    }
} 
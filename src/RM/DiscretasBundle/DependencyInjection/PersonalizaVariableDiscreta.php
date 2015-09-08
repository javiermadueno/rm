<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 10/02/2015
 * Time: 11:41
 */

namespace RM\DiscretasBundle\DependencyInjection;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Entity\VidCriterioGlobal;
use RM\DiscretasBundle\Entity\VidGrupoSegmento;
use RM\DiscretasBundle\Entity\VidRepository;
use RM\DiscretasBundle\Entity\VidSegmento;
use RM\DiscretasBundle\Entity\VidSegmentoGlobal;

class PersonalizaVariableDiscreta
{
    const PERSONALIZADO = 1;
    const NO_PERSONALIZADO = 0;

    /**
     * @var VidRepository
     */
    private $repository;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var VidCriterioGlobal
     */
    private $criteriosGlobales;

    public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
        $this->repository = $this->em->getRepository('RMDiscretasBundle:Vid');
        $this->criteriosGlobales = $this->repository->obtenerCriteriosGlobales()[0];
    }

    /**
     * Marca el VidGrupoSegmentos asociado a la variable como personalizado y
     * copia los segmentos globales, asociandolos a dicho grupo.
     *
     * @param Vid $variable
     *
     * @return bool
     */
    public function personalizaVariable(Vid $variable)
    {

        if(!$vidGrupo = $this->getVidGrupoSegmento($variable->getIdVid())) {
            return false;
        }

        $vidGrupo->setPersonalizado(self::PERSONALIZADO);

        if($variable->getSolicitaTiempo() == Vid::SOLICITA_N) {
            $vidGrupo->setMesesN($this->criteriosGlobales->getReferenciaN());
        }
        else {
            $vidGrupo->setMesesM($this->criteriosGlobales->getMesesM());
            $vidGrupo->setMesesN($this->criteriosGlobales->getMesesN());
        }

        if( Tipo::HABITOS_COMPRA != $variable->getTipo()->getCodigo() ) {
            $this->copiaSegmentosGlobalesA($vidGrupo);
        }


        $this->em->persist($vidGrupo);
        $this->em->flush();

        return true;
    }

    /**
     * Marca el vidGrupoSegmento asociado a la variable como NO personalizado y
     * elimina los segmentos asociados
     *
     * @param Vid $variable
     *
     * @return bool
     */
    public function despersonalizarVariable(Vid $variable)
    {
        if(!$vidGrupo = $this->getVidGrupoSegmento($variable->getIdVid())) {
            return false;
        }

        $vidGrupo->setPersonalizado(self::NO_PERSONALIZADO);

        if(Tipo::HABITOS_COMPRA != $variable->getTipo()->getCodigo()) {
            $this->eliminaSegmentos($vidGrupo);
        }

        $this->em->persist($vidGrupo);
        $this->em->flush();

        return true;
    }

    /**
     * @param $idVid
     * @return VidGrupoSegmento|null
     */
    public function getVidGrupoSegmento($idVid)
    {
        $vidGrupo = $this->repository
            ->obtenerUnicoGrupoSegmentoByVid($idVid);

        if(!$vidGrupo instanceof VidGrupoSegmento) {
            return null;
        }

        return $vidGrupo;
    }


    /**
     * Copia los segmentos Globales al grupo
     *
     * @param VidGrupoSegmento $vidGrupoSegmento
     */
    public function copiaSegmentosGlobalesA(VidGrupoSegmento $vidGrupoSegmento)
    {
        $segmentosGlobales = $this->repository->obtenerSegmentosGlobales();

        /**
         * @var  VidSegmentoGlobal $segmento
         */
        foreach($segmentosGlobales as $segmento)
        {
            $this->creaVidSegmentoAPartirde($segmento, $vidGrupoSegmento);
        }
    }

    /**
     * Crea un VidSegmento copiado de un SegmentoGlobal y se los asigna al VidGrupoSegmento
     *
     * @param VidSegmentoGlobal $vidSegmentoGlobal
     * @param VidGrupoSegmento $vidGrupoSegmento
     */
    private function creaVidSegmentoAPartirde(VidSegmentoGlobal $vidSegmentoGlobal, VidGrupoSegmento $vidGrupoSegmento)
    {
        if(is_null($vidGrupoSegmento) || is_null($vidGrupoSegmento)) {
            return;
        }

        $vidSegmento = new VidSegmento();
        $vidSegmento
            ->setIdVidGrupoSegmento($vidGrupoSegmento)
            ->setNombre($vidSegmentoGlobal->getNombre())
            ->setCondicion($vidSegmentoGlobal->getCondicion())
            ->setPivote($vidSegmentoGlobal->getPivote())
            ->setEstado(1);

        $this->em->persist($vidSegmento);
        $this->em->flush();
    }

    /**
     * Elimina los segmentos asociados al VidGrupoSegmento
     *
     * @param VidGrupoSegmento $vidGrupo
     */
    private function eliminaSegmentos(VidGrupoSegmento $vidGrupo)
    {
       $this->repository->eliminarSegmentosDelGrupo($vidGrupo);
    }

} 
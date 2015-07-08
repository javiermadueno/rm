<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/06/2015
 * Time: 14:12
 */

namespace RM\ComunicacionBundle\DomainManager;

use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository;
use RM\AppBundle\Manager\AbstractManager;

class InstanciaComunicacionManager extends AbstractManager
{
    /**
     * @var InstanciaComunicacionRepository
     */
    private  $repo;

    public function getRepository()
    {
        if(!$this->repo) {
            $this->repo = $this->em->getRepository('RMComunicacionBundle:InstanciaComunicacion');
        }

        return $this->repo;
    }

    public function save(InstanciaComunicacion $instancia)
    {
        $this->em->persist($instancia);
        $this->em->flush($instancia);
    }

    public function remove(InstanciaComunicacion $instancia)
    {
        $instancia->setEstado(-1);
        $this->save($instancia);
    }

    public function update(InstanciaComunicacion $instancia)
    {
        $this->save($instancia);
    }

    public function findBy(array $criteria)
    {
        return $this->repo->findBy($criteria);
    }


} 
<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PlantillaRepository extends EntityRepository
{
    public function obtenerPlantillaById($id_plantilla)
    {
        $dql = "SELECT p
            FROM RMPlantillaBundle:Plantilla p
			WHERE p.idPlantilla = :idplantilla
			AND	  p.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idplantilla', $id_plantilla);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerGSByIdPlantilla($id_plantilla)
    {
        $dql = "SELECT g
            FROM RMPlantillaBundle:GrupoSlots g
			WHERE g.idPlantilla = :idplantilla
			AND	  g.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idplantilla', $id_plantilla);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerGSById($id_grupo)
    {
        $dql = "SELECT g
            FROM RMPlantillaBundle:GrupoSlots g
			WHERE g.idGrupo = :idgrupo
			AND	  g.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idgrupo', $id_grupo);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerSlotByIdGrupo($id_grupo)
    {
        $dql = "SELECT s
            FROM RMPlantillaBundle:Slot s
			WHERE s.idGrupo = :idgrupo
			AND	  s.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idgrupo', $id_grupo);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerSlotById($id_slot)
    {
        $dql = "SELECT s
            FROM RMPlantillaBundle:Slot s
			WHERE s.idSlot = :idslot
			AND	  s.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idslot', $id_slot);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerGruposConNumeroSlots($id_plantilla)
    {
        $dql = "SELECT g.idGrupo, g.nombre, g.tipo, COUNT(s.idSlot) As numSlots, g.tipo as tipo
            FROM RMPlantillaBundle:GrupoSlots g
			JOIN RMPlantillaBundle:Slot s
            WHERE g.idGrupo = s.idGrupo
			AND   g.idPlantilla = :idplantilla
			AND	  g.estado > -1
			AND   s.estado > -1
			GROUP BY g.idGrupo
            ORDER BY g.nombre";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idplantilla', $id_plantilla);

        $registros = $query->getResult();

        return $registros;
    }

    /**
     * @param $id_canal
     *
     * @return array
     */
    public function findPlantillasModeloByCanal($id_canal)
    {
        if ($id_canal == -1) {
            return $this->findAllPlantillasModelo();
        }

        $plantillas = $this->createQueryBuilder('p')
            ->where('p.esModelo = true')
            ->andWhere('p.estado > -1')
            ->andWhere('p.canal = :id_canal')
            ->setParameter('id_canal', $id_canal)
            ->getQuery()->getResult();

        return $plantillas;
    }

    /**
     * @return array
     */
    public function findAllPlantillasModelo()
    {
        $plantillas = $this->createQueryBuilder('p')
            ->where('p.esModelo = true')
            ->andWhere('p.estado > -1')
            ->getQuery()->getResult();

        return $plantillas;
    }
}
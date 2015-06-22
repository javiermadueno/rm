<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class GrupoSlotsRepository extends EntityRepository
{
    public function obtenerGrupoSlotsById($id_grupoSlots)
    {

        $em = $this->getEntityManager();

        $dql = "SELECT p
            FROM RMPlantillaBundle:GrupoSlots p
			WHERE p.idGrupo = :idGrupo";

        $query = $em->createQuery($dql);
        $query->setParameter('idGrupo', $id_grupoSlots);

        $registros = $query->getResult();

        return $registros;
    }

    public function eliminarGSById($id_grupo_slots)
    {

        $em = $this->getEntityManager();


        $dql = "
				UPDATE RMPlantillaBundle:GrupoSlots gs
				SET gs.estado = -1
				WHERE gs.idGrupo IN (:idGrupoSlots)";

        $query = $em->createQuery($dql)
            ->setParameter('idGrupoSlots', $id_grupo_slots);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerGrupoSlotsCreatividadConPromocion($id_instancia)
    {

        $em = $this->getEntityManager();

        $dql = "SELECT gs
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1 AND gs.tipo = :tipo)
            WHERE ic.estado > -1
            AND   ic.idInstancia = :idInstancia
            ORDER BY gs.nombre
            ";

        $query = $em->createQuery($dql)
            ->setParameter('tipo', GrupoSlots::CREATIVIDADES)
            ->setParameter('idInstancia', $id_instancia);

        return $query->getResult();

    }


    public function obtenerGrupoSlotsCreatividadPromocionConNumeroSlots($id_instancia)
    {

        $em = $this->getEntityManager();

        $dql = "SELECT gs.idGrupo, gs.nombre, gs.tipo, COUNT(s.idSlot) As numSlots
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1 AND gs.tipo = :tipo)
            JOIN RMPlantillaBundle:Slot s WITH (gs.idGrupo = s.idGrupo AND s.estado > -1)
            WHERE ic.estado > -1
            AND   ic.idInstancia = :idInstancia
			GROUP BY gs.idGrupo
            ORDER BY gs.nombre";

        $query = $em->createQuery($dql)
            ->setParameter('tipo', GrupoSlots::CREATIVIDADES)
            ->setParameter('idInstancia', $id_instancia);

        return $query->getResult();
    }

    public function findGruposSlotsByComunicacion($id_comunicacion = 0)
    {
        if (!$id_comunicacion) {
            return null;
        }

        $dql = "
            SELECT gs
            FROM RMPlantillaBundle:GrupoSlots gs
            INNER JOIN gs.idPlantilla p
            INNER JOIN RMComunicacionBundle:Comunicacion c WITH (c.plantilla = p)
            WHERE c.idComunicacion = :id_comunicacion
            AND p.estado > -1
            AND gs.estado > -1
            AND c.estado > -1
        ";

        return $this->_em
            ->createQuery($dql)->setParameter('id_comunicacion', $id_comunicacion)
            ->getResult();

        /*
        return $this->_em
            ->createQueryBuilder('gs')
            ->innerJoin('gs.idPlantilla', 'p')
            ->where('p.idComunicacion = :id_comunicacion AND p.estado > -1')
            ->andWhere('gs.estado > -1')
            ->setParameter('id_comunicacion', $id_comunicacion)
            ->getQuery()->getResult();*/
    }

    public function findGruposSlotsByPlantilla($idPlantilla)
    {
        $dql = "
            SELECT gs
            FROM RMPlantillaBundle:GrupoSlots gs
            WHERE gs.idPlantilla = :id_plantilla
            AND gs.estado > -1
        ";

        return $this->_em->createQuery($dql)
            ->setParameter('id_plantilla', $idPlantilla)
            ->getResult();
    }
}
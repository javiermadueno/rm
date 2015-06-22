<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TamanyoImagenRepository extends EntityRepository
{
    public function obtenerTIById($id_tamanyo)
    {

        $dql = "SELECT t
            FROM RMPlantillaBundle:TamanyoImagen t
			WHERE t.idTamanyo IN (" . $id_tamanyo . ")
			AND	  t.estado > -1";

        $query = $this->_em->createQuery($dql);
        $registros = $query->getResult();
        return $registros;
    }

    public function obtenerTIByTipo($tipo)
    {


        $dql = "SELECT t
            FROM RMPlantillaBundle:TamanyoImagen t
			WHERE t.tipo IN (" . $tipo . ")
			AND	  t.estado > -1";

        $query = $this->_em->createQuery($dql);
        $registros = $query->getResult();
        return $registros;
    }

    public function obtenerTIConInfoAsocByTipo($tipo)
    {

        $dql = "SELECT t.idTamanyo, t.codigo, t.ancho, t.alto, gs.idGrupo As idGrupo
            FROM RMPlantillaBundle:TamanyoImagen t
			LEFT JOIN RMPlantillaBundle:GrupoSlots AS gs WITH (t.idTamanyo = gs.idTamanyoImgProducto AND gs.estado > -1)
			WHERE t.tipo IN (" . $tipo . ")
			AND	  t.estado > -1
			GROUP BY t.idTamanyo";

        $query = $this->_em->createQuery($dql);
        $registros = $query->getResult();
        return $registros;
    }
}
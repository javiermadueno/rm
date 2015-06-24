<?php
namespace RM\LinealesBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Entity\VidGrupoSegmento;
use RM\LinealesBundle\Entity\Vil;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;


class Sociodemograficas
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;


    public function __construct(DoctrineManager $doctrine)
    {

        $this->em = $doctrine->getManager();


    }

    /**
     * @return array
     */
    public function  obtenerVariableSociodemograficas()
    {
        $socioDemograficasLineales = $this->buscaVariablesSociodemograficasLineales();
        $socioDemograficasDiscretas = $this->buscaVariablesSociodemograficasDiscretas();

        return array_merge($socioDemograficasLineales, $socioDemograficasDiscretas);
    }

    /**
     * @param string $nombre
     *
     * @return array
     */
    public function findVariablesSociodemograficasPorNombre($nombre = '')
    {
        if (empty($nombre)) {
            return $this->obtenerVariableSociodemograficas();
        }

        $lineales = $this->findVariablesSociodemograficasLinealesByNombre($nombre);
        $discretas = $this->findVariablesSociodemograficasDiscretasByNombre($nombre);

        return array_merge($lineales, $discretas);
    }

    /**
     * @return array
     */
    private function buscaVariablesSociodemograficasLineales()
    {
        $dql_lineales = "
            SELECT DISTINCT  vs.idVil as id, v.nombre as nombre, v.descripcion as descripcion, 0 as discreta
              FROM RMLinealesBundle:VilSegmento vs
              LEFT JOIN RMLinealesBundle:Vil v WITH (v.idVil = vs.idVil)
              LEFT JOIN RMDiscretasBundle:Tipo t WITH (v.tipo = t.id AND t.codigo = :codigoSociodemograficas)
        ";
        $query = $this->em->createQuery($dql_lineales)->setParameter('codigoSociodemograficas', Tipo::SOCIODEMOGRAFICO);
        $lineales = $query->getResult();

        return $lineales;
    }

    /**
     * @return array
     */
    private function buscaVariablesSociodemograficasDiscretas()
    {
        $dql_discretas = "
            SELECT DISTINCT v.idVid as id, v.nombre as nombre, v.descripcion as descripcion, 1 AS discreta
              FROM RMDiscretasBundle:Vid v
            LEFT JOIN   RMDiscretasBundle:VidSegmento vs WITH (vs.estado > 0)
            LEFT JOIN RMDiscretasBundle:VidGrupoSegmento vgs WITH (v.idVid = vgs.idVid and vgs.idVidGrupoSegmento = vs.idVidGrupoSegmento and vgs.estado > 0 and vgs.personalizado = 1)
            LEFT JOIN RMDiscretasBundle:Tipo t WITH (v.tipo = t.id)
            WHERE t.codigo = :codigoSociodemograficas
        ";


        $query = $this->em->createQuery($dql_discretas)->setParameter('codigoSociodemograficas',
            Tipo::SOCIODEMOGRAFICO);
        $discretas = $query->getResult();

        return $discretas;
    }

    /**
     * @param $nombre
     *
     * @return mixed
     */
    public function findVariablesSociodemograficasLinealesByNombre($nombre)
    {

        $dql_lineales = "
            SELECT DISTINCT  vs.idVil as id, v.nombre as nombre, v.descripcion as descripcion, 0 as discreta
              FROM RMLinealesBundle:VilSegmento vs
              LEFT JOIN RMLinealesBundle:Vil v WITH (v.idVil = vs.idVil)
              LEFT JOIN RMDiscretasBundle:Tipo t WITH (v.tipo = t.id AND t.codigo = :codigoSociodemograficas)
             WHERE v.nombre LIKE :nombre
        ";

        $query = $this->em->createQuery($dql_lineales)
            ->setParameter('codigoSociodemograficas', Tipo::SOCIODEMOGRAFICO)
            ->setParameter('nombre', sprintf('%%%s%%', $nombre));

        $lineales = $query->getResult();

        return $lineales;
    }

    public function findVariablesSociodemograficasDiscretasByNombre($nombre)
    {
        $dql_discretas = "
            SELECT DISTINCT v.idVid as id, v.nombre as nombre, v.descripcion as descripcion, 1 AS discreta
              FROM RMDiscretasBundle:Vid v
            LEFT JOIN   RMDiscretasBundle:VidSegmento vs WITH (vs.estado > 0)
            LEFT JOIN RMDiscretasBundle:VidGrupoSegmento vgs WITH (v.idVid = vgs.idVid and vgs.idVidGrupoSegmento = vs.idVidGrupoSegmento and vgs.estado > 0 and vgs.personalizado = 1)
            LEFT JOIN RMDiscretasBundle:Tipo t WITH (v.tipo = t.id)
            WHERE t.codigo = :codigoSociodemograficas
            AND v.nombre LIKE :nombre
            ";

        $query = $this->em->createQuery($dql_discretas)
            ->setParameter('codigoSociodemograficas', Tipo::SOCIODEMOGRAFICO)
            ->setParameter('nombre', sprintf('%%%s%%', $nombre));

        $discretas = $query->getResult();

        return $discretas;
    }

    public function findDatosVariableSocioDemograficaLineal(Vil $variable = null)
    {
        if (!$variable instanceof Vil) {
            throw new InvalidArgumentException();
        }

        $datosVariable = $this->em->getRepository('RMLinealesBundle:VilSegmento')->findBy(
            [
                'idVil' => $variable->getidVil()
            ]
        );

        return $datosVariable;
    }

    public function findDatosVariableSociodemograficaDiscreta(Vid $variable = null)
    {
        if (!$variable instanceof Vid) {
            throw new InvalidArgumentException();
        }

        $grupoSegmento = $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')->findOneBy(
            [
                'idVid' => $variable->getIdVid()
            ]
        );

        $datosVariable = $this->em->getRepository('RMDiscretasBundle:VidSegmento')->findBy(
            [
                'idVidGrupoSegmento' => $grupoSegmento->getIdVidGrupoSegmento()
            ]
        );

        return $datosVariable;
    }

}

?>
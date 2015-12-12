<?php
/**
 * Created by PhpStorm.
 * User: javi
 * Date: 03/07/15
 * Time: 18:05
 */

namespace RM\ProcesosBundle\Factory;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use IMAG\LdapBundle\User\LdapUser;
use RM\ProcesosBundle\Entity\EstadoProceso;
use RM\ProcesosBundle\Entity\Proceso;
use RM\ProcesosBundle\Entity\TipoProceso;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ProcesoFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $token;

    /**
     * @param ManagerRegistry       $manager
     * @param TokenStorageInterface $token
     *
     * @throws \Exception
     */
    public function __construct(ManagerRegistry $manager, TokenStorageInterface $token)
    {
        $this->em = $manager->getManager('procesos');
        $this->token = $token->getToken();
    }

    /**
     * @param $tipo
     *
     * @return Proceso
     */
    public function createProcesoTipo($tipo)
    {
        $usuario = $this->getUsuario();
        $estadoCreado = $this->getEstadoCreado();
        $tipo = $this->getTipoProceso($tipo);

        $proceso = new Proceso();
        $proceso->setFechaCreacion(new \DateTime())
            ->setEstadoProceso($estadoCreado)
            ->setUidUsuario($usuario->getUsername())
            ->setTipoProceso($tipo)
            ->setIdCentro($usuario->getCliente());

        $this->save($proceso);

        return $proceso;
    }

    /**
     * @param \DateTime $fecha|null
     *
     * @return Proceso
     */
    public function createProcesoEnvioEmail(\DateTime $fecha = null)
    {
        $usuario = $this->getUsuario();
        $estadoCreado = $this->getEstadoCreado();
        $tipo = $this->getTipoProceso(TipoProceso::P07);

        $proceso = new Proceso();
        $proceso
            ->setFechaCreacion(new \DateTime())
            ->setIdCentro($usuario->getCliente())
            ->setUidUsuario($usuario->getUsername())
            ->setTipoProceso($tipo)
            ->setEstadoProceso($estadoCreado)
            ->setFechaInicio($fecha)
        ;

        $this->save($proceso);

        return $proceso;
    }

    /**
     * @return EstadoProceso
     */
    private function getEstadoCreado()
    {
        $estado = $this->em
            ->getRepository('ProcesosBundle:EstadoProceso')
            ->findOneBy(['codigo' => EstadoProceso::ESTADO_CREADO]);

        if (!$estado instanceof EstadoProceso) {
            throw new NotFoundHttpException(sprintf(
                "No se ha encontrado el estado creado"
            ));
        }

        return $estado;
    }

    /**
     * @param $codigo
     *
     * @return mixed
     */
    private function getTipoProceso($codigo)
    {
        $tipo = $this->em
            ->getRepository('ProcesosBundle:TipoProceso')
            ->findOneBy(['codigo' => $codigo]);

        if (!$tipo instanceof TipoProceso) {
            throw new NotFoundHttpException(sprintf(
                "No se ha encontrado porceso con código '%s'", $codigo
            ));
        }

        return $tipo;
    }

    /**
     * @return LdapUser
     */
    private function getUsuario()
    {
        return $this->token->getUser();
    }

    /**
     * @param Proceso $proceso
     */
    public function save(Proceso $proceso)
    {
        $this->em->persist($proceso);
        $this->em->flush($proceso);
    }


} 
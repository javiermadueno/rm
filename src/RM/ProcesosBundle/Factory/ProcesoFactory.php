<?php
/**
 * Created by PhpStorm.
 * User: javi
 * Date: 03/07/15
 * Time: 18:05
 */

namespace RM\ProcesosBundle\Factory;

use Doctrine\ORM\EntityManager;
use IMAG\LdapBundle\User\LdapUser;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProcesosBundle\Entity\EstadoProceso;
use RM\ProcesosBundle\Entity\Proceso;
use RM\ProcesosBundle\Entity\TipoProceso;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ProcesoFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @param DoctrineManager       $manager
     * @param TokenStorageInterface $token
     *
     * @throws \Exception
     */
    public function _construct(DoctrineManager $manager, TokenStorageInterface $token)
    {
        $this->em = $manager->getManager();
        $this->token = $token;
    }

    public function createProcesoTipo0()
    {
        $usuario = $this->getUsuario();
        $estadoCreado = $this->getEstadoCreado();
        $tipo0 = $this->getTipoProceso00();

        $proceso = new Proceso();
        $proceso->setFechaCreacion(new \DateTime())
            ->setEstadoProceso($estadoCreado)
            ->setUidUsuario($usuario->getUsername())
            ->setTipoProceso($tipo0)
            ->setIdCentro($usuario->getCliente());

        $this->em->persist($proceso);
        $this->em->flush();
    }

    /**
     * @return EstadoProceso
     */
    private function getEstadoCreado()
    {
        $estado = $this->em
            ->getRepository('RMProcesosBundle:EstadoProceso')
            ->findOneBy(['codigo' => 'cr']);

        if (!$estado instanceof EstadoProceso) {
            throw new NotFoundHttpException(sprintf(
                "No se ha encontrado el estado creado"
            ));
        }

        return $estado;
    }

    /**
     * @return TipoProceso
     */
    private function getTipoProceso00()
    {
        $tipo = $this->em
            ->getRepository('RMProcesosBundle:TipoProceso')
            ->findOneBy(['codigo' => TipoProceso::P00]);

        if (!$tipo instanceof TipoProceso) {
            throw new NotFoundHttpException(sprintf(
                "No se ha enconytado el tipo de proceso 00"
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


} 
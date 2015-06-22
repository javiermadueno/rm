<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/04/2015
 * Time: 12:46
 */

namespace RM\AppBundle\DependencyInjection;


use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use IMAG\LdapBundle\User\LdapUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class DoctrineManager
{
    /**
     * @var EntityManager
     */
    private $em;

    private $cliente;

    /**
     * @param ManagerRegistry          $doctrine
     * @param SecurityContextInterface $security
     *
     * @throws \Exception
     */
    public function __construct(ManagerRegistry $doctrine, SecurityContextInterface $security)
    {
        /** @var TokenInterface $token */
        $token = $security->getToken();
        /** @var LdapUser $usuario */
        $usuario = $token->getUser();
        /** @var  $cliente */
        $this->cliente = $usuario->getCliente();

        if (!isset($this->cliente)) {
            throw new \Exception(
                'No está definida la variable de conexión'
            );
        }

        $this->em = $doctrine->getManager($this->cliente);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|EntityManager
     * @throws \Exception
     */
    public function getManager()
    {
        if (!$this->em) {
            throw new \Exception(sprintf(
                'No se ha encontrado Entity Manager para el cliente %s',
                $this->cliente
            ));
        }

        return $this->em;
    }
} 
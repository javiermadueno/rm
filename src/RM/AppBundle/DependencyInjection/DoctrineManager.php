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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DoctrineManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|EntityManager
     */
    private $em;

    private $cliente;

    private $security;

    /**
     * @param ManagerRegistry       $doctrine
     * @param TokenStorageInterface $security
     *
     * @throws \Exception
     */
    public function __construct(ManagerRegistry $doctrine, TokenStorageInterface $security)
    {
        $this->security = $security;

        /** @var TokenInterface $token */
        $token = $security->getToken();
        /** @var LdapUser $usuario */
        $usuario = $token->getUser();

        $this->cliente = $usuario instanceof LdapUser ? $usuario->getCliente() : null;

        $this->doctrine = $doctrine;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|EntityManager
     * @throws \Exception
     */
    public function getManager()
    {
        if (!isset($this->cliente)) {
            return null;
        }

        $this->em = $this->doctrine->getManager($this->cliente);

        if (!$this->em) {
            throw new \Exception(sprintf(
                'No se ha encontrado Entity Manager para el cliente %s',
                $this->cliente
            ));
        }

        return $this->em;
    }

    /**
     * @return string|null
     */
    public function getCliente()
    {
        if (!isset($this->cliente)) {
            /** @var TokenInterface $token */
            $token = $this->security->getToken();
            /** @var LdapUser $usuario */
            $usuario = $token->getUser();
            $this->cliente = $usuario instanceof LdapUser ? $usuario->getCliente() : null;
        }

        return $this->cliente;
    }

} 
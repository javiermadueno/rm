<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/04/2015
 * Time: 13:04
 */

namespace RM\AppBundle\DependencyInjection;


use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use IMAG\LdapBundle\User\LdapUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MongoManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    private $dm;

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

        $this->dm = $doctrine->getManager();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     * @throws \Exception
     */
    public function getManager()
    {
        if (!$this->dm) {
            throw new \Exception(sprintf(
                'No se ha encontrado Entity Manager para el cliente %s',
                $this->cliente
            ));
        }

        return $this->dm;
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 02/06/2015
 * Time: 16:42
 */
namespace RM\AppBundle\EventListener;

use Doctrine\DBAL\Connection;
use IMAG\LdapBundle\User\LdapUser;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class ChangeDoctrineConnectionListener
 *
 * @package RM\AppBundle\EventListener
 */
class ChangeDoctrineConnectionListener
{
    /**
     * @var LdapUser
     */
    private $user;
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param ContainerInterface    $container
     * @param TokenStorageInterface $security
     * @param Connection            $connection
     * @param array                 $connections
     */
    public function __construct(ContainerInterface $container, TokenStorageInterface $security, Connection $connection, array $connections)
    {
        $this->container = $container;
        $this->security = $security;
        $this->connection = $connection;
        $this->connections = $connections;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if(!$event->isMasterRequest()) {
            return;
        }
        $token = $this->security->getToken();
        $this->user  = $token ? $token->getUser(): null;

        if (!$this->user || is_string($this->user)) {
            return;
        }

        $cliente = $this->user->getCliente();

        if (!$cliente) {
            return;
        }

        $connection = $this->connection;
        $parameters = $this->connection->getParams();

        if (!array_key_exists($cliente, $this->connections)) {
            return;
        }

        $new_param = $this->container->get($this->connections[$cliente]);
        $new_param = $new_param->getParams();

        if ($connection->isConnected()) {
            $connection->close();
        }

        $connection->__construct($new_param, $connection->getDriver(), $connection->getConfiguration(),
            $connection->getEventManager());

        $connection->connect();
    }
} 
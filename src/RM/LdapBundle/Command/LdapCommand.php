<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 01/06/2015
 * Time: 12:14
 */

namespace RM\LdapBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class LdapCommand extends ContainerAwareCommand
{
    /**
     * @var resource
     */
    protected  $connection;

    /**
     * @var bool
     */
    protected  $bind;

    public function __construct()
    {
        parent::__construct();
    }

    protected function connect()
    {
        $this->connection = @ldap_connect('192.168.100.229');

        if (!$this->connection) {
            throw new \Exception('No se ha podido conectar con el ldap');
        }

        $this->bind = @ldap_bind($this->connection, 'cn=admin,dc=relationalmessages,dc=com', 'icca');

        if (!$this->bind) {
            throw new \Exception('No se ha podido conectar con el ldap');
        }
    }
} 
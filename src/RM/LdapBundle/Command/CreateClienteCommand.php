<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/05/2015
 * Time: 13:14
 */
namespace RM\LdapBundle\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClienteCommand extends LdapCommand
{
    protected function configure()
    {
        $this
            ->setName('ldap:client:create')
            ->setDescription('Crea un nuevo cliente en el LDAP')
            ->addArgument(
                'id_cliente',
                InputArgument::REQUIRED,
                '¿Cual es el ID del cliente?'
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect();
        $id_cliente = $input->getArgument('id_cliente');
        $output->writeln(sprintf('<info>Creando el cliente con id "%s"</info>', $id_cliente));

        if (!$this->findCliente($id_cliente)) {
            if ($this->ldap_createGroup($id_cliente)) {
                $output->writeln(sprintf('<info>El cliente "%s" se ha creado corretamente</info>', $id_cliente));
            } else {
                $output->writeln(sprintf('<error>No se ha podido crear el cliente "%s"</error>', $id_cliente));
            }
        } else {
            $output->writeln("<error>El cliente ya existe</error>");
        }


        $param = [
            'id_cliente' => 'carrefour',
            'driver'     => "mysql",
            'host'       => '192.168.100.62',
            'port'       => '',
            'dbname'     => 'rm_test',
            'user'       => 'root',
            'password'   => '',
            'charset'    => 'UTF8'

        ];

        //$this->getContainer()->get('rm.doctrine_configuration')->addEntityManagerParamters($param);

    }

    public function findCliente($id_cliente)
    {
        $dn = "ou=clientes,dc=relationalmessages,dc=com";
        $filter = sprintf("(&(cn=%s))", $id_cliente);

        $search = ldap_search($this->connection, $dn, $filter);

        $cliente = ldap_get_entries($this->connection, $search);

        try {
            if ($cliente ['count'] == 1 || $cliente['count'] > 1) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    private function ldap_createGroup($object_name)
    {
        $dn = "cn={$object_name},ou=clientes,dc=relationalmessages,dc=com";
        $addgroup_ad['cn'] = (string)$object_name;
        $addgroup_ad['o'] = (string)$object_name;
        $addgroup_ad['member'] = [''];
        $addgroup_ad['objectClass'][0] = "top";
        $addgroup_ad['objectClass'][1] = "groupOfNames";


        ldap_add($this->connection, $dn, $addgroup_ad);

        if (ldap_error($this->connection) == "Success") {
            return true;
        } else {
            return false;
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/05/2015
 * Time: 14:30
 */

namespace RM\LdapBundle\Command;


use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;


class CreateUsuarioCommand extends LdapCommand
{
    protected function configure()
    {
        $this
            ->setName('ldap:user:create')
            ->setDescription('Crea un nuevo usuario y lo asgina a la empresa indicada')
            ->addArgument('id_cliente', InputArgument::REQUIRED, 'Empresa a la que pertenece el cliente');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect();

        $id_cliente = $input->getArgument('id_cliente');

        $helper = $this->getHelper('question');

        //Username
        $username_question = new Question('Introduce el nombre de usuario: ', 'username');
        $username_question->setValidator(function ($username) {
            if ($this->findUser($username)) {
                throw new \RuntimeException  (sprintf('El usuario "%s" ya existe', $username));
            }

            return $username;
        });
        $username = $helper->ask($input, $output, $username_question);

        //Nombre
        $nombre_question = new Question('Nombre: ', 'nombre');
        $nombre = $helper->ask($input, $output, $nombre_question);

        //Apellidos
        $apellidos_question = new Question('Apellidos: ', 'apellidos');
        $apellidos = $helper->ask($input, $output, $apellidos_question);

        //Email
        $email_question = new Question('Email: ', 'email');
        $email = $helper->ask($input, $output, $email_question);

        //Telefono
        $telefono_question = new Question('Telefono: ', 'telefono');
        $telefono = $helper->ask($input, $output, $telefono_question);

        //Contraseña
        $pass_question = new Question('Introduce password: ', 'password');
        $pass_question
            ->setHidden(true)
            ->setValidator(function ($answer) {
                if (trim($answer) === '') {
                    throw new \Exception('La contraseña no puede estar vacia');
                }

                return $answer;
            });
        $pass = $helper->ask($input, $output, $pass_question);

        $pass_confirm_question = new Question('Confirma password: ');
        $pass_confirm_question
            ->setHidden(true)
            ->setValidator(function ($answer) use ($pass) {
                if (trim($answer) === '') {
                    throw new \RuntimeException('La contraseña no puede estar vacia');
                }

                if ($pass != $answer) {
                    throw new \RuntimeException('Las contraseñas no coinciden');
                }

                return $answer;
            })
            ->setMaxAttempts(3);

        $helper->ask($input, $output, $pass_confirm_question);

        //Rol
        $rol_question = new ChoiceQuestion(
            'Selecciona un rol: ',
            ['category_manager', 'workflow_manager'],
            0
        );
        $rol_question->setErrorMessage('El rol "%s" no es valido');
        $rol = $helper->ask($input, $output, $rol_question);

        $table = new Table($output);
        $table
            ->setHeaders(['Propiedad', 'Valor'])
            ->setRows([
                ['username', $username],
                ['apellidos', $apellidos],
                ['Email', $email],
                ['telefono', $telefono],
                ['rol', $rol]
            ]);
        $table->render();

        $confirm = new ConfirmationQuestion(
            'Se va a crear un usuario nuevo con los siguientes datos. ¿Desea continuar? [Y|n]',
            false
        );

        if (!$helper->ask($input, $output, $confirm)) {
            return;
        }



        $user = $this->createUser([
            'cn'              => $username,
            'displayName'     => $nombre . ' ' . $apellidos,
            'mail'            => $email,
            'givenName'       => $nombre,
            'sn'              => $apellidos,
            'telephoneNumber' => $telefono,
            'uid'             => $username,
            'userPassword'    => $pass,
        ]);

        if (!$user) {
            throw new \RuntimeException('No se ha podido crear el usuario');
        }

        $this->assignRol($username, $rol);
        $this->assignCompany($username, $id_cliente);

        $output->writeln('Usuario creado correctamente');

    }

    public function findUser($username)
    {
        $base_cn = "ou=usuarios,dc=relationalmessages,dc=com";
        $filter = sprintf('(&(uid=%s))', $username);
        $search = ldap_search($this->connection, $base_cn, $filter);

        $usuario = ldap_get_entries($this->connection, $search);

        try {
            if ($usuario ['count'] == 1 || $usuario['count'] > 1) {
                return true;
            }
        } catch (\Exception $e) {
            return true;
        }

        return false;
    }

    protected function createUser($user_data = [])
    {
        $cn_username = "uid={$user_data['cn']},ou=usuarios,dc=relationalmessages,dc=com";
        $user_data['objectClass'] = array_reverse(['inetOrgPerson', 'shadowAccount', 'top']);
        ldap_add($this->connection, $cn_username, $user_data);

        if (ldap_error($this->connection) == "Success") {
            return true;
        } else {
            return false;
        }
    }

    protected function assignRol($cn_username, $rol)
    {
        $cn_username = "uid={$cn_username},ou=usuarios, dc=relationalmessages, dc=com";
        $cn = "cn={$rol},ou=roles, dc=relationalmessages, dc=com";

        $ldap_rol['member'] = $cn_username;

        ldap_mod_add($this->connection, $cn, $ldap_rol);

    }

    public function assignCompany($cn_username, $empresa)
    {
        $cn_username = "uid={$cn_username},ou=usuarios, dc=relationalmessages, dc=com";
        $cn = "cn={$empresa},ou=clientes, dc=relationalmessages, dc=com";

        $ldap_cliente['member'] = $cn_username;

        ldap_mod_add($this->connection, $cn, $ldap_cliente);
    }

}
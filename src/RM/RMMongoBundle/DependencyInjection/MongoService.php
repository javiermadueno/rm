<?php

namespace RM\RMMongoBundle\DependencyInjection;


use IMAG\LdapBundle\User\LdapUser;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MongoService
{
    /**
     * @var TokenStorageInterface
     */
    protected $sc;

    /**
     * @var \MongoClient
     */
    protected $mongo;

    /**
     * @var \MongoDB
     */
    protected $database;

    /**
     * @var \MongoCollection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var LdapUser
     */
    protected $user;

    /**
     * @var array
     */
    protected $config;


    /**
     * @param TokenStorageInterface $security
     * @param array                 $config
     */
    public function __construct(TokenStorageInterface $security, array $config)
    {
        $this->sc = $security;
        $this->config = $config;
        $this->user = $this->sc->getToken()->getUser();
        $this->getParameters();
        $this->connect();
    }

    public function connect()
    {
        $this->mongo = new \MongoClient($this->parameters['host']);
        $this->database = $this->mongo->selectDB($this->parameters['database']);
    }


    public function getParameters()
    {

        if (!$this->parameters) {
            $this->parameters = [];
        }

        $config = $this->config;

        $resolver = new OptionsResolver();
        $resolver->setDefaults($config['connections'][$config['default_connection']]);

        $connections = $config['connections'];

        $cliente = strtolower($this->user->getCliente());

        if (array_key_exists($cliente, $connections)) {
            $this->parameters = $resolver->resolve($connections[$cliente]);
            return;
        }

        $default_connection = $config['default_connection'];
        $this->parameters = $connections[$default_connection];

    }

}

?>
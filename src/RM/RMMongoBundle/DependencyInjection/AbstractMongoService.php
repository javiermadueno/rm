<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 02/12/2015
 * Time: 11:34
 */

namespace RM\RMMongoBundle\DependencyInjection;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractMongoService
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $centro;

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
    protected $parameters = [];

    public function __construct($centro = '', array $config)
    {
        if(empty($centro)) {
            throw new \Exception("El parametro centro es necesario");
        }

        $this->config = $config;
        $this->centro = $centro;

        $this->getParameters();
        $this->connect();

    }

    public function getParameters()
    {
        $config = $this->config;

        $resolver = new OptionsResolver();
        $default_connection_key = $config['default_connection'];
        $resolver->setDefaults($config['connections'][$default_connection_key]);

        $connections = $config['connections'];

        $centro = strtolower($this->centro);

        if(array_key_exists($centro, $connections)){
            $this->parameters = $resolver->resolve($connections[$centro]);
            return;
        }

        $this->parameters = $connections[$default_connection_key];
    }

    function __destruct()
    {
        $this->mongo->close(true);
    }

    public function connect()
    {
        $this->mongo = new \MongoClient($this->parameters['host']);
        $this->database = $this->mongo->selectDB($this->parameters['database']);
    }
} 
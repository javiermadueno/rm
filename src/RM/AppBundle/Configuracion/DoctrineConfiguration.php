<?php

/**
 * Created by PhpStorm.
 * User=> jmadueno
 * Date=> 01/06/2015
 * Time=> 17=>09
 */
namespace RM\AppBundle\Configuracion;


use Symfony\Component\Yaml\Yaml;

class DoctrineConfiguration
{
    private $path;

    private $yaml;

    public function __construct($path = '')
    {
        $this->path = $path . '/config/config.yml';
        $this->yaml = Yaml::parse(file_get_contents($this->path));
    }

    public function addEntityManagerParamters($parameters = [])
    {
        if (empty($parameters) || empty($this->yaml)) {
            return;
        }

        if (!isset($this->yaml['doctrine']['dbal']['connections'])) {
            return;
        }

        if (array_key_exists($parameters['id_cliente'], $this->yaml['doctrine']['dbal']['connections'])) {
            return;
        }

        $this->yaml['doctrine']['dbal']['connections'][$parameters['id_cliente']] = $this->createConnectionParam($parameters);

        $this->writeConfigFile();

    }


    private function createConnectionParam($params = [])
    {
        return [
            'driver'   => "mysql",
            'host'     => $params['host'],
            'port'     => $params['port'],
            'dbname'   => $params['dbname'],
            'user'     => $params['user'],
            'password' => $params['password'],
            'charset'  => 'UTF8'
        ];
    }

    private function writeConfigFile()
    {
        file_put_contents($this->path, Yaml::dump($this->yaml, 10));
    }

} 
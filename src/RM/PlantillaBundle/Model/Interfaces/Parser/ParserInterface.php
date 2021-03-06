<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/03/2015
 * Time: 16:39
 */

namespace RM\PlantillaBundle\Model\Interfaces\Parser;


use RM\ClienteBundle\Entity\Cliente;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;

interface ParserInterface
{
    /**
     * Rellena el codigo de una plantilla con los datos correspondientes de cada cliente.
     * (HTML, Email, Correo Postal, Banner...)
     *
     * @param   InstanciaComunicacion  $comunicacion
     * @param   Cliente             $cliente
     * @return  mixed
     */
    public function parse(InstanciaComunicacion $comunicacion, Cliente $cliente);
} 
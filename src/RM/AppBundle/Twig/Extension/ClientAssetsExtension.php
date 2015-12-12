<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/09/2015
 * Time: 11:38
 */
namespace RM\AppBundle\Twig\Extension;

use RM\AppBundle\ClientPathUrlGenerator\ClientPathUrlGenerator;

class ClientAssetsExtension extends \Twig_Extension
{
    /**
     * @var ClientPathUrlGenerator
     */
    private  $generator;

    public function __construct(ClientPathUrlGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ruta_imagen_creatividad', [$this, 'getRutaImagenCreatividad']),
            new \Twig_SimpleFunction('ruta_imagen_producto', [$this, 'getRutaImagenProducto'])
        ];
    }



    public function getRutaImagenCreatividad($creatividad, $absolute = false)
    {
        return $this
            ->generator
            ->getRutaImagenCreatividad($creatividad, $absolute);
    }

    public function getRutaImagenProducto($producto, $absolute = false)
    {
        return $this
            ->generator
            ->getRutaImagenProducto($producto, $absolute);
    }

    public function getName()
    {
        return 'client_asset';
    }


} 
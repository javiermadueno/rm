<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/04/2015
 * Time: 10:32
 */

namespace RM\InsightBundle\Twig\Extension;

class MonedaExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('moneda', array($this, 'monedaFilter'), array(
                    'is_safe' => array('html'),
                    'pre_escape' => 'html'
                )
            ),
            new \Twig_SimpleFilter('porcentaje', array($this, 'porcentajeFilter'), array(
                    'is_safe' => array('html'),
                    'pre_escape' => 'html'
                ))
        );
    }

    public function monedaFilter($valor) {
        if (!$valor || !is_numeric($valor)) {
            $valor = 0.00;
        }

        return sprintf('%s %s',number_format($valor, 2, ',', '.') , '&euro;');
    }

    public function porcentajeFilter($valor)
    {
        if (!$valor || !is_numeric($valor)) {
            $valor = 0.00;
        }

        return sprintf('%s %s',number_format($valor, 2, ',', '.') , '%');
    }

    public function getName()
    {
        return 'moneda';
    }
} 
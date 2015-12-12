<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/11/2015
 * Time: 11:41
 */

namespace RM\AppBundle\Twig\Extension;


class IdFormatterExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('id', [$this, 'idFilter'], [
                'is_safe' => array('html'),
                'pre_escape' => 'html'
            ])
        ];
    }

    public function idFilter($value)
    {
        if(!is_numeric($value)) {
            return $value;
        }

        return sprintf("#%07d", $value);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'id_formatter';
    }
}
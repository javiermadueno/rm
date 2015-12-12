<?php

namespace RM\ProductoBundle\Twig\Extension;
 
class TwigExtension extends \Twig_Extension
{/**
     * Return the functions registered as twig extensions
     * 
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'file_exists' => new \Twig_SimpleFunction('file_exists', 'file_exists'),
        );
    }

    public function getName()
    {
        return 'twig_extension';
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 02/07/2015
 * Time: 12:10
 */

namespace RM\AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePickerType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text'
        ));
    }

    public function getName()
    {
        return 'date_picker';
    }

    public function getParent()
    {
        return 'date';
    }

} 
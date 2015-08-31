<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 07/07/2015
 * Time: 11:53
 */

namespace RM\ProductoBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', [
            'required' => false,
            'label'    => 'imagen',
                'attr' => [
                    'accept' => $this->mimesPermitidos()
                ]
        ])
            ->add('submit', 'submit', ['label' => 'boton.actualizar'])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\Producto'
        ]);
    }

    public function mimesPermitidos()
    {
        $extensionFormatoImages = [
            "image/jpeg", "image/jpg", "image/gif", "image/tiff", "image/bmp", "image/png"
        ];
        return implode(', ' , $extensionFormatoImages);

    }

    public function getName()
    {
        return 'producto';
    }

} 
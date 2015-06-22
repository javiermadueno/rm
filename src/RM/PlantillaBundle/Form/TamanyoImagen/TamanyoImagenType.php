<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 20/02/2015
 * Time: 11:21
 */

namespace RM\PlantillaBundle\Form\TamanyoImagen;


use Doctrine\ORM\EntityRepository;
use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TamanyoImagenType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'class'         => 'RM\PlantillaBundle\Entity\TamanyoImagen',
            'em'            => $_SESSION['connection'],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->where('t.tipo = :tipo')
                    ->andWhere('t.estado > -1')
                    ->setParameter('tipo', TamanyoImagen::PRODUCTO);
            },
            'property'      => 'codigo',
            'empty_value'   => 'Tamaño de Imagen',
            'empty_data'    => null,
            'required'      => false,
            'label'         => 'Tamaño de Imagen'
        ]);
    }


    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'tamanyoImagen';
    }

} 
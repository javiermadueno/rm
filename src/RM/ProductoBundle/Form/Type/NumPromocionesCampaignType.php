<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/07/2015
 * Time: 14:14
 */

namespace RM\ProductoBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumPromocionesCampaignType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('segmentadas', 'collection', [
                'allow_add'    => true,
                'allow_delete' => true,
                'type'         => new PromocionSegmentadaCampanaType(),
                'options'      => [
                    'em' => $options['em']
                ]
            ])
            ->add('genericas', 'collection', [
                'allow_add'    => true,
                'allow_delete' => true,
                'type'         => new PromocionGenericaCampanaType(),
                'options'      => [
                    'em' => $options['em']
                ]
            ])
        ;


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\NumPromociones'
        ])
            ->setRequired(['em'])
            ->setAllowedTypes(['em' => 'Doctrine\Common\Persistence\ObjectManager' ]);
    }

    public function getName()
    {
        return 'num_promocion';
    }

} 
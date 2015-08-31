<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/07/2015
 * Time: 16:45
 */

namespace RM\ProductoBundle\Form\Type;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numPromociones', 'collection', [
            'type'    => new NumPromocionesCampaignType(),
            'options' => [
                'em' => $options['em']
            ],
            'allow_add'    => false,
            'allow_delete' => false,
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ComunicacionBundle\Entity\InstanciaComunicacion'
        ])
            ->setRequired(['em'])
            ->setAllowedTypes(['em' => 'Doctrine\Common\Persistence\ObjectManager' ]);
    }

    public function getName()
    {
        return 'campaign';
    }

} 
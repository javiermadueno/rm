<?php

namespace RM\TransformadasBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VtIntervaloType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idVil', 'entity', [
                'required'      => true,
                'em'            => $_SESSION['connection'],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->where('v.estado > -1');
                },
                'class' => 'RM\LinealesBundle\Entity\Vil'
            ])
            ->add('condicion', 'choice', [
                'required' => true,
                'choices'  => [
                    "1" => '<',
                    '2' => '>',
                    '3' => '<=',
                    '4' => '>=',
                    '5' => '='
                ]
            ])
            ->add('pivote', 'integer', ['required' => true])
            ->add('factor', 'number', ['required' => true])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\TransformadasBundle\Entity\VtIntervalo'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_transformadasbundle_vtintervalo';
    }
}

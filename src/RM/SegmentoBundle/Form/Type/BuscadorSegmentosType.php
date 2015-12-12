<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 24/08/2015
 * Time: 9:23
 */


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use RM\DiscretasBundle\Entity\Tipo;

class BuscadorSegmentosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', 'entity', [
                'data_class' => 'RM\DiscretasBundle\Entity\Tipo',
                'em' => $options['em'],
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repo) {
                    return $repo->createQueryBuilder('tipo')
                                ->where('tipo.codigo != :codigo_rfm')
                                ->setParameter('codigo_rfm', Tipo::RFM)
                                ->orderBy('tipo.nombre');
                },
                'attr' => [
                    'id' => 'tipo'
                ]
            ]);

        $builder->get('tipo')->addEventListener(\Symfony\Component\Form\FormEvents::POST_SUBMIT, function(\Symfony\Component\Form\FormEvent $event){
            $form = $event->getForm()->getParent();
            $tipo = $event->getData();

            if(!$tipo instanceof Tipo) {
                return;
            }

            $form->add('variable', 'entity', []);

        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['em']);
    }

    public function getName()
    {
        return 'buscador_segmentos';
    }

} 
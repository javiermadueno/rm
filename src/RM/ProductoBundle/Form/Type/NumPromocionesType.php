<?php

namespace RM\ProductoBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumPromocionesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numSegmentadas', 'number', [
                'required' => true
            ])
            ->add('numGenericas', 'number', [
                'required' => true
            ])
            ->add('estado', 'hidden', ['data' => 1])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options) {
                $numPromocion = $event->getData();
                $form         = $event->getForm();

                $nivel = $options['nivel_categoria'];

                if (!$numPromocion instanceof NumPromociones) {
                    return;
                }

                $instancia = $numPromocion->getIdInstancia();
                $grupo = $numPromocion->getIdGrupo();

                $form->add('idCategoria', 'entity', [
                    'class'         => 'RM\CategoriaBundle\Entity\Categoria',
                    'em'            => $options['em'],
                    'property'      => 'nombre',
                    'required'      => true,
                    'query_builder' => function (EntityRepository $er) use ($instancia, $nivel, $grupo) {

                        return $er->createQueryBuilder('c')
                            ->leftJoin('RMProductoBundle:NumPromociones', 'np', 'with',
                                'np.idCategoria = c.idCategoria AND np.idInstancia = :id_instancia AND np.idGrupo = :id_grupo')
                            ->where('c.idNivelCategoria = :nivel_categoria')
                            ->andWhere('c.estado > -1')
                            ->andWhere('c.asociado = 1')
                            ->andWhere('np.idNumPro = null')
                            ->orderBy('c.nombre')
                            ->setParameters([
                                'nivel_categoria' => $nivel,
                                'id_instancia'    => $instancia->getIdInstancia(),
                                'id_grupo'        => $grupo->getIdgrupo()
                            ]);
                    }
                ]);

            });

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'RM\ProductoBundle\Entity\NumPromociones',
            'nivel_categoria' => 1
        ]);

        $resolver->setRequired([
            'em',
            'nivel_categoria'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'numpromocion';
    }
}

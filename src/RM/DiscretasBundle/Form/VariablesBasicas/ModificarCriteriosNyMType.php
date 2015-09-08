<?php

namespace RM\DiscretasBundle\Form\VariablesBasicas;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModificarCriteriosNyMType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('referenciaN', 'integer', [
				'label' => 'Tiempo Referencia N',
				'required' => true,
		])
		->add('mesesN', 'integer', [
			'label' => 'N',
			'required' => true,
		])
		->add('mesesM', 'integer', [
				'label' => 'M',
				'required' => true,
		]);
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\DiscretasBundle\Entity\VidCriterioGlobal'
        ]);
    }



	public function getName()
	{
		return 'criterio';
	}
}


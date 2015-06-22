<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/04/2015
 * Time: 12:45
 */

namespace RM\PlantillaBundle\Form\Handler;


use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\DomainManager\GrupoSlotManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use RM\PlantillaBundle\Form\GrupoSlotsType;
use RM\PlantillaBundle\Form\GrupoSlotsCreatividadType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateGrupoSlotFormHandler
{
    /**
     * @var GrupoSlotManager
     */
    private $manager;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var OptionsResolver
     */
    public $resolver;

    public function __construct (GrupoSlotManager $manager, FormFactoryInterface $factory)
    {
        $this->manager = $manager;
        $this->factory = $factory;
        $this->resolver = new OptionsResolver();
        $this->setDefaultOptions();
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     */
    public function handle(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $grupo = $form->getData();

        $this->manager->create($grupo);

        return true;
    }

    /**
     * @param GrupoSlots $grupo
     * @param array $options
     * @return FormInterface
     */
    public function createForm(GrupoSlots $grupo, array $options)
    {
        $options = $this->resolveOptions($options);
        $form = $this->factory->create(new GrupoSlotsType(), $grupo, $options);
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @param GrupoSlots $grupo
     * @param array $options
     * @return FormInterface
     */
    public function createCreatividadForm(GrupoSlots $grupo, array $options)
    {
        $options = $this->resolveOptions($options);
        $grupo->setTipo(GrupoSlots::CREATIVIDADES);

        $form = $this->factory->create(new GrupoSlotsCreatividadType(), $grupo, $options);
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @param GrupoSlots $grupo
     * @param array $options
     * @return FormInterface
     */
    public function createPromocionForm(GrupoSlots $grupo, array $options)
    {
        $options  = $this->resolveOptions($options);

        $form = $this->factory->create(new GrupoSlotsType(), $grupo, $options);
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     *
     */
    private function setDefaultOptions()
    {
        $this->resolver
            ->setRequired(array('action', 'em', 'method'))
            ->setDefaults(array(
                'method' => 'POST'
            ))
            ->addAllowedTypes(array(
                'em'=> 'Doctrine\Common\Persistence\ObjectManager'
            ))

        ;
    }

    /**
     * @param array $options
     * @return array
     */
    private function resolveOptions(array $options)
    {
        return $this->resolver->resolve($options);
    }




} 
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/04/2015
 * Time: 11:23
 */

namespace RM\PlantillaBundle\Form\Handler;


use RM\PlantillaBundle\DomainManager\PlantillaManager;
use RM\PlantillaBundle\Form\PlantillaType;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CreatePlantillaFormHandler
{
    /**
     * @var PlantillaManager
     */
    private $manager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param PlantillaManager     $manager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(PlantillaManager $manager, FormFactoryInterface $formFactory)
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     */
    public function handle(FormInterface $form, Request $request)
    {
        if (!$request->isMethod('POST')) {
            return false;
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $plantilla = $form->getData();

        $this->manager->create($plantilla);

        return true;
    }

    public function createCreateForm(PlantillaInterface $plantilla)
    {

        $form = $this->formFactory->create(new PlantillaType(), $plantilla, [
            'method' => 'POST'
        ]);


        return $form;

    }

} 
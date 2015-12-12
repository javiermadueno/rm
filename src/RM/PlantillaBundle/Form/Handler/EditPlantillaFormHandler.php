<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/04/2015
 * Time: 11:38
 */

namespace RM\PlantillaBundle\Form\Handler;


use RM\PlantillaBundle\DomainManager\PlantillaManager;
use RM\PlantillaBundle\Form\PlantillaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EditPlantillaFormHandler
{
    /**
     * @var PlantillaManager
     */
    private $manager;

    /**
     * @param PlantillaManager     $manager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(PlantillaManager $manager, FormFactoryInterface $formFactory)
    {
        $this->manager     = $manager;
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
        if (!$request->isMethod('POST') || !$request->isMethod('PUT')) {
            return false;
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $plantilla = $form->getData();

        $this->manager->update($plantilla);

        return true;
    }


    public function createEditForm($id)
    {
        $plantilla = $this->manager->find($id);

        $form = $this->formFactory->create(new PlantillaType(), $plantilla, [
            'method' => 'PUT'
        ]);
    }
} 
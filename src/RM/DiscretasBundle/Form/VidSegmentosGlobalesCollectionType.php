<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 26/05/2015
 * Time: 17:41
 */

namespace RM\DiscretasBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VidSegmentosGlobalesCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('segmentos', 'collection', [
            'type'         => new VidSegmentoGlobalType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'label'        => false
        ]);
    }

    public function getName()
    {
        return 'rm_discretas_vid_segmentos_globales';
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 17/02/2015
 * Time: 9:53
 */

namespace RM\PlantillaBundle\Form\DataTransformer;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

abstract class EntityToNumberTransformer implements DataTransformerInterface
{

    /**
     * @var string Clase de la entidad
     */
    protected $entityClass;

    /**
     * @var string Nombre del repositorio de la entidad
     */
    protected $entityRepository;

    /**
     * @var ObjectManager EntityManager
     */
    protected $em;


    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param   Object $entity
     *
     * @return  int
     */
    abstract protected function getId($entity);

    /**
     * @param   int $id
     *
     * @return  Object
     */
    abstract protected function getEntity($id);

    /**
     * @param mixed $entity
     *
     * @return mixed|void
     */
    public function transform($entity)
    {
        if (null === $entity || !$entity instanceof $this->entityClass) {
            return '';
        }

        return $this->getId($entity);
    }

    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->getEntity($id);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'La entidad de la clase "%s" con id "%s" no existe.',
                $this->entityClass,
                $id
            ));
        }

        return $entity;
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/06/2015
 * Time: 14:37
 */

namespace RM\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RMController extends Controller
{
    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager
     * @throws \Exception
     */
    protected function getManager()
    {
        return $this->get('rm.manager')->getManager();
    }

} 
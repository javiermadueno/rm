<?php

namespace RM\DiscretasBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\DiscretasBundle\Entity\Configuracion;
use RM\DiscretasBundle\Entity\ConfiguracionRepository;
use RM\DiscretasBundle\Entity\ParametroConfiguracion;


class ConfiguracionService
{
    /**
     * @var EntityManager
     */
    private $em;

	public function __construct(DoctrineManager $doctrine)
    {
		$this->em = $doctrine->getManager();
	}
	
	public function getConfigurationParameters()
    {
		$registros = $this->em->getRepository('RMDiscretasBundle:Configuracion')->getConfigurationParameters();
		return $registros;
	}
	
	
	public function saveConfigurationParameters($parameters)
    {
        /** @var ConfiguracionRepository $conguracionRepo */
        $conguracionRepo = $this->em->getRepository('RMDiscretasBundle:Configuracion' );
        foreach ($parameters as $id => $parameter) {
            $parametro = $conguracionRepo->find($id);

            if(!$parametro instanceof Configuracion) {
                return false;
            }

            $valor = $parameter['valor'];

            if($parametro->getNombre() === 'nivel_category_manager') {
                $valor_max = $this->em->createQueryBuilder()
                    ->select('MAX(nivel.idNivelCategoria)')
                    ->from('RMCategoriaBundle:NivelCategoria', 'nivel')
                    ->getQuery()->getSingleScalarResult();

                $valor = min($valor, $valor_max);

            }

            if(!is_numeric($valor)) {
                continue;
            }

            $parametro->setValor($valor);
            $this->em->persist($parametro);
        }

        $this->em->flush();
        return true;
	}

    public function findAllParametrosConfiguracion()
    {
        return $this->em->getRepository('RMDiscretasBundle:ParametroConfiguracion')->findAll();
    }


    public function guardaParametrosConfiguracion($parametros = [])
    {
        if(empty($parametros)) {
            return;
        }

        foreach($parametros as $id => $parametro)
        {
            $parametroConfiguracion = $this->em->find('RMDiscretasBundle:ParametroConfiguracion', $id);

            if(!$parametroConfiguracion instanceof ParametroConfiguracion) {
                continue;
            }

            $parametroConfiguracion
                ->setMaxBajo($parametro['maxBajo'])
                ->setMaxMedio($parametro['maxMedio'])
            ;

            $this->em->persist($parametroConfiguracion);
        }

        $this->em->flush();
    }
}
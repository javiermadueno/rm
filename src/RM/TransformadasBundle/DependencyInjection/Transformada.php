<?php

namespace RM\TransformadasBundle\DependencyInjection;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\TransformadasBundle\Entity\Vt;
use RM\TransformadasBundle\Entity\VtGrupo;
use RM\TransformadasBundle\Entity\VtIntervalo;
use RM\TransformadasBundle\Entity\VtSegmento;
use Symfony\Component\HttpFoundation\Request;


class Transformada
{

    private $em;
	public function __construct(DoctrineManager $manager)
    {
        $this->em  = $manager->getManager();
    }
	
	public function getTransformadas($nombre = '', $tipoVar = 0)
	{
		$registros = $this->em->getRepository('RMTransformadasBundle:Vt')->obtenerVariablesTransformadas($nombre, $tipoVar);
		return $registros;
	}
	
	public function getVTbyId($id_vt)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerVTbyId($id_vt);
	
		return $registros;
	}

	
	public function getSegmentoById($id_vt_segmento)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerSegmentoVTbyId($id_vt_segmento);
	
		return $registros;
	}
	
	public function getGrupoById($id_grupo)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerGrupoVTbyId($id_grupo);
	
		return $registros;
	}
	
	public function getIntervaloById($id_intervalo)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerIntervaloVTbyId($id_intervalo);
	
		return $registros;
	}

	public function getSegmentosByIdVt($id_vt)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerSegmentosVTbyIdVt($id_vt);
	
		return $registros;
	}
	
	public function getGruposByIdSegmento($id_vt_segmento)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerGruposVTbyIdSegmento($id_vt_segmento);
	
		return $registros;
	}
	
	public function getIntervalosByIdGrupo($id_grupo)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerIntervalosVTbyIdGrupo($id_grupo);
	
		return $registros;
	}

	public function getGruposByIdVt($id_vt)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerGruposVTbyIdVt($id_vt);
	
		return $registros;
	}
	
	public function getIntervalosByIdVt($id_vt)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$registros = $repo->obtenerIntervalosVTbyIdVt($id_vt);
	
		return $registros;
	}
	
	public function getArrayRowSpanSegmentosGruposVT($id_vt)
	{
		$arrayTmp = [];
		
		$obtenerGrupos = $this->getGruposByIdVt($id_vt);
		foreach($obtenerGrupos as $objGr){
			$selInter = $this->getIntervalosByIdGrupo($objGr->getIdGrupo());
			$numInter = count($selInter);
			
			$arrayTmp["g_". $objGr->getIdGrupo()] = $numInter;
			
			if (array_key_exists('s_'.$objGr->getIdVtSegmento()->getIdVtSegmento(), $arrayTmp)){
				$conTotal = $arrayTmp['s_'.$objGr->getIdVtSegmento()->getIdVtSegmento()] + $numInter;
				$arrayTmp['s_'.$objGr->getIdVtSegmento()->getIdVtSegmento()] = $conTotal;
			}
			else{
				$arrayTmp['s_'.$objGr->getIdVtSegmento()->getIdVtSegmento()] = $numInter;
			}
		}
		
		return $arrayTmp;		
	}
	
	
	public function guardarObjeto($objeto)
	{
		$this->em->persist($objeto);
		$this->em->flush();
	
		return $objeto;
	}
	
	public function guardarCambios()
	{
		$this->em->flush();
	}
	
	public function guardarSegGrupoVIbyPost(Request $request)
	{
		$objSegmentos = $this->getSegmentosByIdVt($request->get('id_vt'));
		
		$arrayObjsVl = [];
		$repoVL = $this->em->getRepository('RMLinealesBundle:Vil');
		
		foreach($objSegmentos as $objSeg){
			
			$objGrupos = $this->getGruposByIdSegmento($objSeg->getIdVtSegmento());
			
			foreach($objGrupos as $objGrupo){
				
				$objIntervalos = $this->getIntervalosByIdGrupo($objGrupo->getIdGrupo());
				
				foreach($objIntervalos as $objIntervalo){
					
					$id_vil = $request->get('var_'. $objIntervalo->getIdIntervalo());
					
					if(empty($arrayObjsVl[$id_vil])){
						$objLineales = $repoVL->obtenerVLbyId($id_vil);
						$objLineal = $objLineales[0];
						$arrayObjsVl[$id_vil] = $objLineal;
					}
					else{
						$objLineal = $arrayObjsVl[$id_vil];
					} 
					
					$objIntervalo->setIdVil($objLineal);
					$objIntervalo->setCondicion($request->get('cond_'. $objIntervalo->getIdIntervalo()));
					$objIntervalo->setPivote($request->get('pivote_'. $objIntervalo->getIdIntervalo()));
					$this->em->persist($objIntervalo);
				}
				
				//$objGrupo->setNombre($request->get('gr_'. $objGrupo->getIdGrupo()));
				$this->em->persist($objGrupo);
			}
			
			$objSeg->setNombre($request->get('seg_'. $objSeg->getIdVtSegmento()));
			$this->em->persist($objSeg);
		}
		
		$this->em->flush();
		
		return 1;
	}

	
	
	public function eliminarSegGrupoVIbyPost($listaSegmentosAEliminar, $listaGruposAEliminar, $listaIntervalosAEliminar, $id_vt)
	{
		/*Se elimina la descendencia de los elementos seleccionados segun sea el tipo pasado*/
		if($listaSegmentosAEliminar != ''){
			$this->eliminarSegmentosVTbyId($listaSegmentosAEliminar);
		}		
		if($listaGruposAEliminar != ''){
			$this->eliminarGruposVTbyId($listaGruposAEliminar);
		}
		if($listaIntervalosAEliminar != ''){			
			$this->eliminarIntervalosVTbyId($listaIntervalosAEliminar);
		}
		
		/*Ahora se comprueba la ascendencia hacia arriba, es decir, si se ha eliminado un intervalo, cuyo grupo padre solo tiene
		  ese hijo, tambi�n se eliminir� el grupo y asi sucesivamente, solamente cuando los padres ya no tengan hijos con estado = 1
		 */
		$this->em->flush();
		
		if($listaIntervalosAEliminar != ''){
			$selectIntervalos = $this->getIntervaloById($listaIntervalosAEliminar);
			$arrayGrup = [];
			$arraySeg = [];
			foreach($selectIntervalos as $selInt){
				array_push($arrayGrup, $selInt->getIdGrupo());
				array_push($arraySeg, $selInt->getIdGrupo()->getIdVtSegmento());
			}
			
			$arrayGrup = array_unique($arrayGrup);
			$arraySeg = array_unique($arraySeg);
			
			foreach($arrayGrup as $idgrupo){
				if($idgrupo->getEstado() == 1) {
					$selectHijosdeGrupos = $this->getIntervalosByIdGrupo($idgrupo->getIdGrupo());
					if($selectHijosdeGrupos == NULL){
						/*Se elimina el grupo porque se ha quedado sin hijos y se almacena los segmentos padres para realizar lo mismo*/
						$idgrupo->setEstado(-1);
						$this->em->persist($idgrupo);
						array_push($arraySeg, $selInt->getIdGrupo()->getIdVtSegmento());
					}
				}
			}
			$this->em->flush();
			if($arraySeg != NULL){
				foreach($arraySeg as $idseg){
					if($idseg->getEstado() == 1) {
						$selectHijosdeSegmentos = $this->getGruposByIdSegmento($idseg->getIdVtSegmento());
						if($selectHijosdeSegmentos == NULL){
							/*Se elimina el grupo porque se ha quedado sin hijos y se almacena los segmentos padres para realizar lo mismo*/
							$idseg->setEstado(-1);
							$this->em->persist($idseg);
						}
					}
				}
				$this->em->flush();
			}
		}
		
		if($listaGruposAEliminar != ''){
			$selectGrupos = $this->getGrupoById($listaGruposAEliminar);
			$arraySeg = [];
			foreach($selectGrupos as $selGrup){
				array_push($arraySeg, $selGrup->getIdVtSegmento());
			}
			$arraySeg = array_unique($arraySeg);
			
			foreach($arraySeg as $idseg){
				if($idseg->getEstado() == 1) {
					$selectHijosdeSegmentos = $this->getGruposByIdSegmento($idseg->getIdVtSegmento());
					if($selectHijosdeSegmentos == NULL){
						/*Se elimina el grupo porque se ha quedado sin hijos y se almacena los segmentos padres para realizar lo mismo*/
						$idseg->setEstado(-1);
						$this->em->persist($idseg);
					}
				}
			}
			$this->em->flush();
		}
		
		return 1;
	}
	
	public function eliminarSegmentosVTbyId($listaSegmentosAEliminar)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objetosSeg = $repo->obtenerSegmentoVTbyId($listaSegmentosAEliminar);
		foreach($objetosSeg as $objetoSeg){
			$selectGrupos = $this->getGruposByIdSegmento($objetoSeg->getIdVtSegmento());
			
			foreach($selectGrupos as $objGrup){
				$this->eliminarGruposVTbyId($objGrup->getIdGrupo());
			}
			
			$objetoSeg->setEstado(-1);
			$this->em->persist($objetoSeg);
		}
	}
	
	public function eliminarGruposVTbyId($listaGruposAEliminar)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objetosGrupo = $repo->obtenerGrupoVTbyId($listaGruposAEliminar);
		foreach($objetosGrupo as $objetoGrupo){
			$selectIntervalos = $this->getIntervalosByIdGrupo($objetoGrupo->getIdGrupo());
				
			foreach($selectIntervalos as $objInter){
				$this->eliminarIntervalosVTbyId($objInter->getIdIntervalo());
			}
				
			$objetoGrupo->setEstado(-1);
			$this->em->persist($objetoGrupo);
		}
	}
	
	public function eliminarIntervalosVTbyId($listaIntervalosAEliminar)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objetosInter = $repo->obtenerIntervaloVTbyId($listaIntervalosAEliminar);
		foreach($objetosInter as $objetoInt){
			$objetoInt->setEstado(-1);
			$this->em->persist($objetoInt);
		}
	}
	
	public function eliminarRamaAscIntervalosVTbyId($listaIntervalosAEliminar)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objetosInter = $repo->obtenerIntervaloVTbyId($listaIntervalosAEliminar);
		
		if($objetosInter){
			$arrayGruposPadres = [];
			foreach($objetosInter as $objetoInt){
				array_push($arrayGruposPadres, $objetoInt->getIdIntervalo());
			}
			$listaGp = implode(",", $arrayGruposPadres);			
		}
	}

	
	public function crearObjSegmento($nombreSeg, $id_vt)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objVarTr = $repo->obtenerVTbyId($id_vt);

		$objSegmento = new VtSegmento();
		$objSegmento->setEstado(1);
		$objSegmento->setIdVt($objVarTr[0]);
		$objSegmento->setNombre($nombreSeg);
		
		$this->em->persist($objSegmento);
		$this->em->flush();
		
		return $objSegmento;
	
	}
	
	public function crearObjGrupo($nombreGrupo, $id_vt_segmento)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objSeg = $repo->obtenerSegmentoVTbyId($id_vt_segmento);
	
		$objGrupo = new VtGrupo();
		$objGrupo->setIdVtSegmento($objSeg[0]);
		//$objGrupo->setNombre($nombreGrupo);
		$objGrupo->setEstado(1);
	
		$this->em->persist($objGrupo);
		$this->em->flush();
	
		return $objGrupo;
	
	}
	
	public function crearObjIntervalo($id_grupo, $id_vil)
	{
		$repo = $this->em->getRepository('RMTransformadasBundle:Vt');
		$objGrup = $repo->obtenerGrupoVTbyId($id_grupo);
		
		$repoVL = $this->em->getRepository('RMLinealesBundle:Vil');
		$objLineal = $repoVL->obtenerVLbyId($id_vil);			
		
	
		$objIntervalo = new VtIntervalo();
		$objIntervalo->setIdGrupo($objGrup[0]);
		$objIntervalo->setIdVil($objLineal[0]);
		$objIntervalo->setEstado(1);
		$objIntervalo->setFactor(1);
	
		$this->em->persist($objIntervalo);
		$this->em->flush();
	
		return $objIntervalo;
	
	}

}
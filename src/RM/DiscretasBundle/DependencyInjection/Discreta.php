<?php

namespace RM\DiscretasBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Entity\VidCriterioGlobal;
use RM\DiscretasBundle\Entity\VidGrupoSegmento;
use RM\DiscretasBundle\Entity\VidSegmento;
use Symfony\Component\HttpFoundation\Request;

class Discreta
{
    protected $mailer;

    public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    public function getDiscretas($nombre = '', $tipoVar = 0)
    {
        $registros = $this->em->getRepository('RMDiscretasBundle:Vid')->obtenerVariablesDiscretas($nombre, $tipoVar);

        return $registros;
    }


    public function getVDbyId($id_vid)
    {
        $registros = $this->em->getRepository('RMDiscretasBundle:Vid')->obtenerVDbyId($id_vid);

        return $registros;
    }

    public function getGSbyIdGrupo($id_vid_grupo_segmento)
    {
        $repoVar  = $this->em->getRepository('RMDiscretasBundle:Vid');
        $registro = $repoVar->obtenerGrupoSegmentoByVidGrupoSegmento($id_vid_grupo_segmento);


        return $registro;
    }

    public function getGSbyIdVid(Vid $id_vid)
    {
        /*
         * Accion:
         * Se obtiene por defecto un grupo de segmento cualquiera mediante la variable id_vid.
         * En el caso de no encontrar ninguno, crea uno por defecto con la configuracion personalizada de la variable.
         * Esto se realiza para entrar por primera vez a la ficha de una variable sin grupo de segmento asignado.
         */

        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $registroGS = $repoVar->obtenerUnicoGrupoSegmentoByVid($id_vid->getIdVid());


        if (!$registroGS)        // No existe ning�n registro en la tabla, por lo tanto se crea uno dependiendo el caso
        {
            $objVid = $id_vid;

            if ($objVid->getClasificacion() === Vid::CLASIFICACION_CATEGORIA)            // Es una categoria
            {
                $repoCat       = $this->em->getRepository('RMCategoriaBundle:Categoria');
                $objCategorias = $repoCat->obtenerCatAsoc();
                $objCategoria  = $objCategorias [0];
                $registroGS    = $this->crearObjGrupoSegmentoConCategoria($objVid, $objCategoria, $personalizado = 0);
            } elseif ($objVid->getClasificacion() === Vid::CLASIFICACION_MARCA)            // Es una marca
            {
                $repoMarca  = $this->em->getRepository('RMProductoBundle:Marca');
                $objMarcas  = $repoMarca->obtenerMarcas();
                $objMarca   = $objMarcas [0];
                $registroGS = $this->crearObjGrupoSegmentoConMarca($objVid, $objMarca);
            } else            // Se crea uno por defecto sin nada
            {
                $registroGS = $this->crearObjGrupoSegmentoPorDefecto($objVid);
            }
        }

        return $registroGS;
    }

    public function crearObjGrupoSegmentoConCategoria(Vid $objVid, $objCategoria, $personalizado)
    {

        /**
         * Se inactiva el anterior grupoSegmento si lo hubiese
         */
        $gruposSegmentos = $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')->findBy([
            'idVid'  => $objVid->getIdVid(),
            'estado' => 1
        ]);

        $lastGroup = $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')
            ->findLastGrupoSegmentoActivo($objVid->getIdVid());

        /** @var $criterioGlobal VidCriterioGlobal */
        $criterioGlobal = $this->em->getRepository('RMDiscretasBundle:Vid')->obtenerCriteriosGlobales()[0];


        $mesesM = $lastGroup instanceof VidGrupoSegmento ? $lastGroup->getMesesM() : $criterioGlobal->getMesesM();
        $mesesN = $lastGroup instanceof VidGrupoSegmento ? $lastGroup->getMesesN() : $criterioGlobal->getMesesN();

        foreach ($gruposSegmentos as $grupo) {
            $grupo->setEstado(-1);
            $this->em->persist($grupo);
        }

        /*
         * Accion: Se crea un grupo de segmento asociandole una categoria. Los demas campos se ponen vacios
         */

        $registroGS = new VidGrupoSegmento ();
        $registroGS->setIdVid($objVid);
        $registroGS->setIdCategoria($objCategoria);
        $registroGS->setMesesM($mesesM);
        $registroGS->setMesesN($mesesN);
        $registroGS->setEstado(1);
        $registroGS->setPersonalizado($personalizado);


        $this->em->persist($registroGS);
        $this->em->flush();

        return $registroGS;
    }

    public function crearObjGrupoSegmentoConMarca(Vid $objVid, $objMarca)
    {
        /**
         * Se inactiva el anterior grupoSegmento si lo hubiese
         */
        $gruposSegmentos = $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')->findBy([
            'idVid' => $objVid->getIdVid()
        ]);

        foreach ($gruposSegmentos as $grupo) {
            $grupo->setEstado(-1);
            $this->em->persist($grupo);
        }
        /*
         * Accion: Se crea un grupo de segmento asociandole una marca. Los demas campos se ponen vacios
         */
        $registroGS = new VidGrupoSegmento ();
        $registroGS->setIdVid($objVid);
        $registroGS->setIdMarca($objMarca);
        $registroGS->setEstado(1);
        $this->em->persist($registroGS);
        $this->em->flush();

        return $registroGS;
    }

    public function crearObjGrupoSegmentoPorDefecto(Vid $objVid)
    {
        /*
         * Accion: Se crea un grupo de segmento sin clasificacion ninguna. Los demas campos se ponen vacios
         */

        $objetoVID = new Vid();
        $objetoVID->setClasificacion($objVid->getClasificacion());
        $objetoVID->setDescripcion($objVid->getDescripcion());
        $objetoVID->setEstado($objVid->getEstado());
        $objetoVID->setNombre($objVid->getNombre());
        $objetoVID->setSolicitaTiempo($objVid->getSolicitaTiempo());
        $objetoVID->setTipo($objVid->getTipo());


        $registroGS = new VidGrupoSegmento ();
        $registroGS->setIdVid($objVid);
        $registroGS->setEstado(1);
        $this->em->persist($registroGS);
        $this->em->flush();

        return $registroGS;
    }

    public function getSegmentosByIdGrupo($id_vid_grupo_segmento)
    {
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $registrosSegmentos = $repoVar->obtenerSegmentosByIdGrupo($id_vid_grupo_segmento);

        return $registrosSegmentos;
    }

    public function getGSByIdVidIdCategoria($id_vid, $id_categoria)
    { /*
	   * Accion:
	   * Devuelve un grupo de segmento dado la id variable y el id categoria
	   */
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $regGrupo = $repoVar->obtenerGSByCatAndVar($id_vid, $id_categoria);

        return $regGrupo;
    }

    public function getGSByIdVidIdMarca($id_vid, $id_marca)
    {
        /*
         * Accion:
         * Devuelve un grupo de segmento dado la id variable y el id marca
         */
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $regGrupo = $repoVar->obtenerGSByMarcaAndVar($id_vid, $id_marca);

        return $regGrupo;
    }

    public function getGSByIdVidIdProveedor($id_vid, $id_proveedor)
    {
        return $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')
            ->createQueryBuilder('gs')
            ->where('gs.idProveedor = :id_proveedor and gs.idVid = :id_vid and gs.estado = 1')
            ->setParameter('id_proveedor', $id_proveedor)
            ->setParameter('id_vid', $id_vid)
            ->getQuery()
            ->getResult();
    }

    public function getGSByIdVidSinClasificacion($id_vid)
    {
        /*
        * Accion:
        * Devuelve un grupo de segmento dado la id variable y el id marca
        */
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $regGrupo = $repoVar->obtenerGSByIdVarSinClasificacion($id_vid);

        return $regGrupo;
    }

    public function getSegmentosGlobales()
    {
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $regSegProp = $repoVar->obtenerSegmentosGlobales();

        return $regSegProp;
    }

    public function getCriteriosGlobales()
    {
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $regCriProp = $repoVar->obtenerCriteriosGlobales();

        return $regCriProp;
    }

    public function eliminarSegmentosAsocByIdSegmento($id_vid_segmento)
    {
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        /*
         * $partesId = preg_split("/[,]+/", $id_vid_segmento);
         * foreach($partesId as $partId){
         * $registrosSegmentos = $repoVar->eliminarSegmentosByIdSegmento($partId);
         * }
         */

        $registrosSegmentos = $repoVar->eliminarSegmentosByIdSegmento($id_vid_segmento);

        $this->em->flush();

        return $registrosSegmentos;
    }

    public function guardarNuevosSegmentosAsocByPost(Request $request)
    {
        $repoVar    = $this->em->getRepository('RMDiscretasBundle:Vid');
        $registroGS = $repoVar->obtenerGrupoSegmentoByVidGrupoSegmento($request->get('id_vid_grupo_segmento'));

        $contSegmentos = $request->get('contSegmentos');
        for ($i = 1; $i <= $contSegmentos; $i++) {
            if ($request->get('nuevoSegNombre' . $i) !== "" && $request->get('nuevoSegPerc' . $i) !== "") {
                $registroS = new VidSegmento ();
                $registroS->setIdVidGrupoSegmento($registroGS [0]);
                $registroS->setNombre($request->get('nuevoSegNombre' . $i));
                $registroS->setCondicion($request->get('nuevoSegCond' . $i));
                $registroS->setPivote(intval($request->get('nuevoSegPerc' . $i)));
                $registroS->setEstado(1);

                $this->em->persist($registroS);
            }
        }
        $this->em->flush();

        return $registroGS;
    }

    public function guardarNuevosSegmentosGlobalesDefectoAsocByPost(Request $request)
    {
        $repoVar    = $this->em->getRepository('RMDiscretasBundle:Vid');
        $registroGS = $repoVar->obtenerGrupoSegmentoByVidGrupoSegmento($request->get('id_vid_grupo_segmento'));

        $numSegAct = $request->get('numSegAct');
        for ($i = 1; $i <= $numSegAct ; $i++) {
            if ($request->get('nomSeg' . $i) !== "" && $request->get('perc' . $i) !== "") {
                $registroS = new VidSegmento ();
                $registroS->setIdVidGrupoSegmento($registroGS [0]);
                $registroS->setNombre($request->get('nomSeg' . $i));
                $registroS->setCondicion($request->get('CondiSeg' . $i));
                $registroS->setPivote(intval($request->get('perc' . $i)));
                $registroS->setEstado(1);

                $this->em->persist($registroS);
            }
        }
        $this->em->flush();

        return $registroGS;
    }

    public function modificarSegmentosAsocByPost(Request $request)
    {
        $repoVar = $this->em->getRepository('RMDiscretasBundle:Vid');

        $partesId = preg_split("/[,]+/", $request->get('idsSegAct'));
        foreach ($partesId as $partId) {
            $registroS = $repoVar->obtenerSegmentoByIdSegmento($partId);
            $registroS [0]->setNombre($request->get('nomSeg' . $partId));
            $registroS [0]->setCondicion($request->get('CondiSeg' . $partId));
            $registroS [0]->setPivote($request->get('perc' . $partId));

            $this->em->persist($registroS [0]);
        }
        $this->em->flush();

        return $registroS [0];
    }

    public function guardarObjeto($objeto)
    {

        $this->em->merge($objeto);
        $this->em->flush();

        return $objeto;
    }

    public function crearObjGrupoSegmentoConProveedor(Vid $objVid, $objProveedor)
    {
        /**
         * Se inactiva el anterior grupoSegmento si lo hubiese
         */
        $gruposSegmentos = $this->em->getRepository('RMDiscretasBundle:VidGrupoSegmento')->findBy([
            'idVid' => $objVid->getIdVid()
        ]);

        foreach ($gruposSegmentos as $grupo) {
            $grupo->setEstado(-1);
            $this->em->persist($grupo);
        }

        /*
		 * Accion: Se crea un grupo de segmento asociandole una marca. Los demas campos se ponen vacios
		 */
        $registroGS = new VidGrupoSegmento ();
        $registroGS->setIdVid($objVid);
        $registroGS->setIdProveedor($objProveedor);
        $registroGS->setEstado(1);
        $this->em->persist($registroGS);
        $this->em->flush();

        return $registroGS;
    }

    public function insertarParametrosConfiguracion($params)
    {


    }
}
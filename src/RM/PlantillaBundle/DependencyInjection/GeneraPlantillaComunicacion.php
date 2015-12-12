<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 19/02/2015
 * Time: 10:01
 */

namespace RM\PlantillaBundle\DependencyInjection;

use RM\AppBundle\ClientPathUrlGenerator\ClientPathUrlGenerator;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\DomCrawler\Crawler;
use Twig_Environment as Twig;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;


class GeneraPlantillaComunicacion
{

    /**
     * @var Twig
     */
    private $twig;


    /**
     * @param Twig                   $twig
     * @param ClientPathUrlGenerator $generator
     */
    public function __construct(Twig $twig, ClientPathUrlGenerator $generator)
    {
        $this->twig = $twig;
        $this->generator = $generator;
        $this->crawler = new Crawler();
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return $this
     */
    public function creaArchivoPlantilla(PlantillaInterface $plantilla)
    {
        $html = $this->renderPlantilla($plantilla);

        $nombreArchivoPlantilla =
            $this->getRutaPlantilla($plantilla);

        if(!file_put_contents($nombreArchivoPlantilla, $html)) {
            throw new FileNotFoundException(
                sprintf('No se ha podido escribir en el fichero %s', $nombreArchivoPlantilla));
        }

        return $this;
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return string
     */
    public function creaArchivoPlantillaTemporal(PlantillaInterface $plantilla)
    {
        $html = $this->renderPlantilla($plantilla);

        $temp = tempnam(sys_get_temp_dir(), 'plantilla');

        if (! file_put_contents($temp, $html)) {
            throw new FileNotFoundException(
                'No se ha podido generar el fichero de plantilla');
        }

        return $temp;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return string
     */
    public function renderPlantilla(PlantillaInterface $plantilla)
    {
        $html = $this->twig->render('RMPlantillaBundle:PlantillaComunicacion:perfumeria/index.html.twig', [
                'plantilla' => $plantilla
            ]);

        return $html;
    }

    /**
     * @return string
     */
    public  function getRutaCarpetaPlantillasCliente()
    {
        $ruta  = $this->generator->getRutaPlantillas();

        if(!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    public function getRutaCarpetaComunicacionesGeneradas()
    {
        $ruta  = $this->generator->getRutaComunicacionesGeneradas();

        if(!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return string
     * @throws \Exception
     */
    public function getRutaPlantilla(PlantillaInterface $plantilla)
    {
        return $this->getRutaCarpetaPlantillasCliente().'/'.$plantilla->getIdPlantilla().'.html';
    }

    /**
     * @param PlantillaInterface $plantilla
     * @param null               $ruta
     *
     * @return array
     * @throws \Exception
     */
    public function compruebaPlantilla(PlantillaInterface $plantilla, $ruta = null)
    {
        if(is_null($ruta)) {
            $rutaPlantilla = $this->getRutaPlantilla($plantilla);
        } elseif (is_string($ruta)) {
            $rutaPlantilla = $ruta;
        } else {
            throw new \Exception('El parametro para comprobar la plantilla debe ser la ruta de la plantilla o la plantilla');
        }


        if(!file_exists($rutaPlantilla)){
           $this->creaArchivoPlantilla($plantilla);
        }

        $error = [];
        $crawler = new Crawler(file_get_contents($rutaPlantilla));


        /** @var GrupoSlots $grupo */
        foreach($plantilla->getGruposSlots() as $grupo) {
            /** @var Slot $slot */
            foreach($grupo->getSlots() as $slot) {

                $id = sprintf('#%s-%s','slot',$slot->getIdSlot());

                $div_slot = $crawler->filter($id);
                if(!count($div_slot)) {
                    $error[] = sprintf('Falta el div con ID = "%s-%s" ',
                        $grupo->getIdGrupo(),$slot->getIdSlot());
                    continue;
                }

                if($grupo->getMImgProducto()) {
                    $id_imagen = sprintf('%s-%s', $id, 'imagen');
                    if(!count($div_slot->filter($id_imagen))) {
                        $error[] = sprintf('Falta el div de imagen para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMNombreProducto()) {
                    $id_nombre_producto = sprintf('%s-%s', $id, 'nombreProducto');
                    if(!count($div_slot->filter($id_nombre_producto))) {
                        $error[] = sprintf('Falta el div de nombreProducto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMPrecio()) {
                    $id_precio = sprintf('%s-%s', $id, 'precio');
                    if(!count($div_slot->filter($id_precio))) {
                        $error[] = sprintf('Falta el div de precio para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMTexto()) {
                    $id_texto = sprintf('%s-%s', $id, 'texto');
                    if(!count($div_slot->filter($id_texto))) {
                        $error[] = sprintf('Falta el div de texto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMCondiciones()) {
                    $id_condiciones = sprintf('%s-%s', $id, 'condiciones');
                    if(!count($div_slot->filter($id_condiciones))) {
                        $error[] = sprintf('Falta el div de condiciones para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMFidelizacion()) {
                    $id_fidelizacion = sprintf('%s-%s', $id, 'fidelzacion');
                    if(!count($div_slot->filter($id_fidelizacion))) {
                        $error[] = sprintf('Falta el div de fidelizacion para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMVoucher()) {
                    $id_voucher = sprintf('%s-%s', $id, 'voucher');
                    if(!count($div_slot->filter($id_voucher))) {
                        $error[] = sprintf('Falta el div de Voucher para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMVolumen()) {
                    $id_volumen = sprintf('%s-%s', $id, 'volumen');

                    if(!count($div_slot->filter($id_volumen))) {
                        $error[] = sprintf('Falta el div de volumen para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

            }
        }

        if(!empty($error)) {
            return $error;
        }

        return [];
    }

} 
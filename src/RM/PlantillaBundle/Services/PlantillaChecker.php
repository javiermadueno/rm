<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/11/2015
 * Time: 16:43
 */

namespace RM\PlantillaBundle\Services;

use RM\AppBundle\ClientPathUrlGenerator\ClientPathUrlGenerator;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;


class PlantillaChecker
{
    private $generator;

    private $fs;

    public function __construct(ClientPathUrlGenerator $generator)
    {
        $this->generator = $generator;
        $this->fs        = new Filesystem();
    }

    /**
     * @param PlantillaInterface $plantilla
     * @param null               $ruta
     *
     * @return array
     * @throws \Exception
     */
    public function check(PlantillaInterface $plantilla, $ruta = null)
    {
        if (is_null($ruta)) {
            $rutaPlantilla = $this->getRutaPlantilla($plantilla);
        } elseif (is_string($ruta)) {
            $rutaPlantilla = $ruta;
        } else {
            throw new \Exception('El parametro para comprobar la plantilla debe ser la ruta de la plantilla o la plantilla');
        }


        if (!file_exists($rutaPlantilla)) {
            throw new IOException(sprintf("No se ha encontrado el fichero de plantilla especificado %s",
                    $rutaPlantilla));
        }

        $error   = [];
        $crawler = new Crawler(file_get_contents($rutaPlantilla));


        /** @var GrupoSlots $grupo */
        foreach ($plantilla->getGruposSlots() as $grupo) {
            /** @var Slot $slot */
            foreach ($grupo->getSlots() as $slot) {

                $id = sprintf('#%s-%s', 'slot', $slot->getIdSlot());

                /*
                $div_slot = $crawler->filter($id);
                if (!count($div_slot)) {
                    $error[] = sprintf('Falta el div con ID = "%s-%s" ',
                        $grupo->getIdGrupo(), $slot->getIdSlot());
                    continue;
                }
                */
                if ($grupo->getMImgProducto()) {
                    $id_imagen = sprintf('%s-%s', $id, 'imagen');
                    if (!count($crawler->filter($id_imagen))) {
                        $error[] = sprintf('Falta el nodo de imagen con id = "%s"', $id_imagen);
                    }
                }

                if ($grupo->getMNombreProducto()) {
                    $id_nombre_producto = sprintf('%s-%s', $id, 'nombreProducto');
                    if (!count($crawler->filter($id_nombre_producto))) {
                        $error[] = sprintf('Falta el nodo de texto con id = "%s"', $id_nombre_producto);
                    }
                }

                if ($grupo->getMPrecio()) {
                    $id_precio = sprintf('%s-%s', $id, 'precio');
                    if (!count($crawler->filter($id_precio))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_precio);
                    }
                }

                if ($grupo->getMTexto()) {
                    $id_texto = sprintf('%s-%s', $id, 'texto');
                    if (!count($crawler->filter($id_texto))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_texto);
                    }
                }

                if ($grupo->getMCondiciones()) {
                    $id_condiciones = sprintf('%s-%s', $id, 'condiciones');
                    if (!count($crawler->filter($id_condiciones))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_condiciones);
                    }
                }

                if ($grupo->getMFidelizacion()) {
                    $id_fidelizacion = sprintf('%s-%s', $id, 'fidelzacion');
                    if (!count($crawler->filter($id_fidelizacion))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_fidelizacion);
                    }
                }

                if ($grupo->getMVoucher()) {
                    $id_voucher = sprintf('%s-%s', $id, 'voucher');
                    if (!count($crawler->filter($id_voucher))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_voucher);
                    }
                }

                if ($grupo->getMVolumen()) {
                    $id_volumen = sprintf('%s-%s', $id, 'volumen');

                    if (!count($crawler->filter($id_volumen))) {
                        $error[] = sprintf('Falta el nodo texto con id = "%s"', $id_volumen);
                    }
                }

            }
        }

        if (!empty($error)) {
            return $error;
        }

        return [];
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaPlantilla(PlantillaInterface $plantilla)
    {
        return $this->generator
            ->getRutaPlantillas() . '/' . $plantilla->getIdPlantilla() . '/index.html';
    }

} 
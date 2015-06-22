<?php

namespace RM\CategoriaBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;
use RM\CategoriaBundle\Entity\Categoria;
use RM\CategoriaBundle\Entity\CategoriaRepository;
use RM\DiscretasBundle\Entity\Configuracion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;


class CategoriaServicio
{
    const CATEGORY = 'ROLE_CATEGORY_MANAGER';
    const WORKFLOW = 'ROLE_WORKFLOW_MANAGER';

    /**
     * @var array
     */
    private $categoriasPermitidas = [];

    private $em;
    /**
     * @var SecurityContextInterface
     */
    private $security;
    /**
     * @var mixed
     */
    private $user;
    /**
     * @var string
     */
    private $rol;
    /**
     * @var string
     */
    private $nivelCategoriaVisible;

    /**
     * @var CategoriaRepository
     */
    private $repo;


    /**
     * @param ManagerRegistry          $doctrine
     * @param SecurityContextInterface $security
     *
     * @throws \Exception
     */
    public function __construct(ManagerRegistry $doctrine, SecurityContextInterface $security)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
        $this->security = $security;
        $this->user = $security->getToken()->getUser();
        $this->repo = $this->em->getRepository('RMCategoriaBundle:Categoria');
        $this->nivelCategoriaVisible = $this->getNivelCategoriasVisible();

        if ($security->isGranted('ROLE_WORKFLOW_MANAGER')) {
            $this->rol = self::WORKFLOW;
        } elseif ($security->isGranted('ROLE_CATEGORY_MANAGER')) {
            $this->rol = self::CATEGORY;
        } else {
            throw new AccessDeniedException("Acceso Denegado");
        }

    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getNivelCategoriasVisible()
    {
        $configuracion = $this->em->getRepository('RMDiscretasBundle:Configuracion')->findOneBy([
            'nombre' => 'nivel_category_manager'
        ]);

        if (!$configuracion instanceof Configuracion) {
            throw new \Exception("No hay definido un nivel visible de categoria para los workflow manager");
        }

        return $configuracion->getValor();
    }

    /**
     * @param $id_categoria
     *
     * @return mixed
     */
    public function getCatById($id_categoria)
    {
        $registros = $this->repo->obtenerCategoria($id_categoria);

        return $registros;

    }

    /**
     * @return mixed
     */
    public function getCategoriasPorNivelVisible()
    {
        if ($this->rol == self::WORKFLOW) {
            return $this->getCategoriasPorNivel($this->nivelCategoriaVisible);
        } else {
            return $this->getCategoriasPorNombreyNivel($this->getCategoriasPermitidas(), $this->nivelCategoriaVisible);
        }
    }

    /**
     * @param $id_nivel
     *
     * @return mixed
     */
    public function getCategoriasPorNivel($id_nivel)
    {
        $registros = $this->repo->obtenerCategorias($id_nivel);

        return $registros;
    }

    /**
     * @param array $nombrecategorias
     * @param       $idNivel
     *
     * @return mixed
     */
    public function getCategoriasPorNombreyNivel($nombrecategorias = [], $idNivel)
    {
        return $this->repo->findCategoriasByNombreYNivel($nombrecategorias, $idNivel);
    }

    /**
     * @return array
     */
    public function getCategoriasPermitidas()
    {
        if (!$this->categoriasPermitidas) {
            $this->getCategoriasPorRol();
        }

        return array_map(
            function (Categoria $categoria) {
                return $categoria->getIdCategoria();
            },
            $this->categoriasPermitidas
        );
    }

    /**
     * @return array
     */
    public function getCategoriasPorRol()
    {

        if ($this->rol == self::WORKFLOW) {
            //Todas las cetgorias asociadas
            $this->categoriasPermitidas = $this->getCatAsociadas();

        } elseif ($this->rol == self::CATEGORY) {
            //Las actegorias que esten asociadas y que coincidan con los nombres
            $this->categoriasPermitidas = $this->getCatAsociadasPorNombre($this->user->getCategorias());
        }

        return $this->categoriasPermitidas;
    }

    /**
     * @return mixed
     */
    public function getCatAsociadas()
    {
        return $this->repo->obtenerCatAsoc();
    }

    /**
     * @param array $nombreCategorias
     *
     * @return mixed
     */
    public function getCatAsociadasPorNombre($nombreCategorias = [])
    {
        return $this->repo->obtenerCategoriasPorNombre($nombreCategorias);
    }

    /**
     * @param $id_instancia
     *
     * @return mixed
     */
    public function getCatByInstancia($id_instancia)
    {
        $registros = [];

        if ($this->rol == self::WORKFLOW) {
            $registros = $this->repo->obtenerCatByInstancia($id_instancia);
        }

        if ($this->rol == self::CATEGORY) {
            $registros = $this->repo->obtenerCatPermitidasByInstancia(
                $id_instancia,
                $this->getCategoriasPermitidas()
            );
        }

        return $registros;

    }

    /**
     * @return mixed
     */
    public function getCategoriasDeCampanya()
    {
        $categorias = [];

        if ($this->rol === self::WORKFLOW) {
            $categorias = $this->repo->obtenerCategoriasDeCampanya();
        }

        if ($this->rol === self::CATEGORY) {
            $categorias = $this->repo->obtenerCategoriasPermitidasDeCampanya($this->getCategoriasPermitidas());
        }

        return $categorias;
    }

    /**
     * @param array $nombreCategorias
     *
     * @return mixed
     */
    public function getCategoriasDeCampanyaByNombre($nombreCategorias = [])
    {
        $registros = $this->repo->obtenerCategoriasDeCampanyaByNombre($nombreCategorias);

        return $registros;
    }

    /**
     * @param Request $request
     */
    public function guardarCategoriasAsocbyPost(Request $request)
    {
        $objCategorias = $this->getCategorias($id_nivel = $request->get('nivel'));

        $categorias = $request->get('categorias');

        /** @var $objCat Categoria */
        foreach ($objCategorias as $objCat) {
            if (array_key_exists($objCat->getIdCategoria(), $categorias)) {
                $objCat->setAsociado($categorias[$objCat->getIdCategoria()]);
                $this->em->persist($objCat);
            }
        }

        $this->em->flush();

    }

    /**
     * @param        $id_nivel
     * @param int    $asociado
     * @param string $id_categoria
     *
     * @return mixed
     */
    public function getCategorias($id_nivel, $asociado = -1, $id_categoria = '')
    {
        $registros = $this->em->getRepository('RMCategoriaBundle:Categoria')
            ->obtenerCategorias($id_nivel, $asociado, $id_categoria);

        return $registros;

    }

    /**
     * @return mixed
     */
    public function getNivelesCategoria()
    {
        $repo = $this->em->getRepository('RMCategoriaBundle:Categoria');
        $registros = $repo->obtenerNivelesCategoria();

        return $registros;
    }
}
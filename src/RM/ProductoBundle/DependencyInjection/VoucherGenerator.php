<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 14/04/2015
 * Time: 17:22
 */

namespace RM\ProductoBundle\DependencyInjection;


use Doctrine\Common\Persistence\ManagerRegistry;

class VoucherGenerator
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function generateUniqueVoucher()
    {
        $voucher = null;

        do {
            $hash = sha1(microtime(true));
            $voucher = strtoupper(substr($hash, mt_rand(0, 33), 8));
        } while ($this->isUsed($voucher));

        return $voucher;
    }

    public function isUsed($code)
    {
        $isUsed = null != $this->em->getRepository('RMProductoBundle:Promocion')->findOneBy([
                'codigo' => $code
            ]);

        return $isUsed;
    }
} 
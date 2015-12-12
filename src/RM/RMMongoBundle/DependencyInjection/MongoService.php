<?php

namespace RM\RMMongoBundle\DependencyInjection;



use IMAG\LdapBundle\User\LdapUser;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MongoService extends AbstractMongoService
{
    /**
     * @var TokenStorageInterface
     */
    protected  $sc;

    /**
     * @var LdapUser
     */
    protected $user;


    /**
     * @param TokenStorageInterface $security
     * @param array                 $config
     */
    public function __construct(TokenStorageInterface $security, array $config)
    {
        $this->user = $security->getToken()->getUser();
        $centro = $this->user->getCliente();
        parent::__construct($centro, $config);
    }

}

?>
<?php

namespace RM\LdapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LdapBundle extends Bundle
{
    public function boot()
    {
        if (!function_exists('ldap_connect')) {
            throw new \Exception("module php-ldap isn't install");
        }
    }
}

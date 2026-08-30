<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    protected ?string $connection = 'default';

    public static array $objectClasses = [
        'top',
        'person',
        'organizationalPerson',
        'inetOrgPerson',
    ];
}
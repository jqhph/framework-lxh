<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\Database\Concerns\HasRoles;
use Lxh\Auth\Database\Concerns\HasAbilities;

trait HasRolesAndAbilities
{
    use HasRoles, HasAbilities {
        HasRoles::getClipboardInstance insteadof HasAbilities;
    }
}

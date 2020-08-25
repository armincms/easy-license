<?php

namespace Armincms\EasyLicense\Policies;

use Armincms\EasyLicense\Manual; 

class EasyLicenseCardLicense extends Policy
{ 
    /**
     * Determine whether the user can sell the license.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\EasyLicense\Manual  $license
     * @return mixed
     */
    public function sell(Authenticatable $user, Manual $license)
    {
        return true;
    }
}

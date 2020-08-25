<?php

namespace Armincms\EasyLicense\Policies; 

use Armincms\EasyLicense\Manual;


class EasyLicenseCard extends Policy
{ 
    use HasActivation; 

    /**
     * Determine whether the user can add the manual model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\EasyLicense\Manual  $license
     * @return mixed
     */
    public function addManual(Authenticatable $user, Manual $manual)
    {
        return true;
    }
}

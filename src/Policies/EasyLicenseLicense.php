<?php

namespace Armincms\EasyLicense\Policies;
 
use Armincms\EasyLicense\Credit;

class EasyLicenseLicense extends Policy
{  
    use HasActivation;

    /**
     * Determine whether the user can sell the credit.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\EasyLicense\Credit  $credit
     * @return mixed
     */
    public function addCreadit(Authenticatable $user, Credit $credit)
    {
        return true;
    }
}

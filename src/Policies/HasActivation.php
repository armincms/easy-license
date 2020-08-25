<?php

namespace Armincms\EasyLicense\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasActivation
{ 
    /**
     * Determine whether the user can sell the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function active(Authenticatable $user, Model $model)
    {
        return true;
    }

    /**
     * Determine whether the user can sell the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function inactive(Authenticatable $user, Model $model)
    {
        return true;
    }
}

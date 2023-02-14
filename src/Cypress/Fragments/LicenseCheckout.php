<?php

namespace Armincms\EasyLicense\Cypress\Fragments;

use Armincms\Contract\Concerns\InteractsWithModel;
use Armincms\Contract\Contracts\Resource;
use Zareismail\Cypress\Contracts\Resolvable;
use Zareismail\Cypress\Fragment;

abstract class LicenseCheckout extends Fragment implements Resolvable, Resource
{
    use InteractsWithModel;

    /**
     * Get the resource Model class.
     *
     * @return
     */
    public function model(): string
    {
        return \Armincms\EasyLicense\Models\License::class;
    }

    /**
     * Find model by given uri.
     *
     * @param  string  $resourceUri
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findModelByUri($request, $resourceUri)
    {
        return $this->newQuery($request)->find($resourceUri);
    }
}

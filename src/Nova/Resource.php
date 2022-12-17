<?php

namespace Armincms\EasyLicense\Nova;

use Laravel\Nova\Resource as BaseResource;

abstract class Resource extends BaseResource
{
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Easy License';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'el-'.parent::uriKey();
    }
}

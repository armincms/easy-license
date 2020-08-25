<?php

namespace Armincms\EasyLicense;
 
use Illuminate\Support\Str;
use Armincms\Targomaan\Translation as Model; 
use Cviebrock\EloquentSluggable\Sluggable;
use Core\HttpSite\Concerns\HasPermalink;

class Translation extends Model 
{ 
    use Sluggable, HasPermalink; 

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seo' => 'json', 
    ]; 
    
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = parent::getTable();

        return Str::startsWith($table, 'el_') ? $table : "el_{$table}";
    } 

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ]; 
    } 

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function component()
    {
        return $this->load('manufacturer')->manufacturer->component(); 
    }
}

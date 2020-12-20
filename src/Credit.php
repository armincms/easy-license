<?php

namespace Armincms\EasyLicense;
  

class Credit extends Model 
{    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
        'expires_on' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function license()
    {
    	return $this->belongsTo(License::class);
    }  

    public function startIfNotStarted()
    { 
        if(! $this->inUse()) {
            $this->markAsInUse();
        }

        return $this; 
    }

    public function inUse()
    {
        return ! is_null($this->expires_on);
    }

    public function markAsInUse()
    {
        $this->forceFill([ 
            'expires_on' => $this->getExpirationDatetime() 
        ])->save();

        return $this;
    }

    public function isExpired()
    {  
        return ! is_null($this->expires_on) && optional($this->expires_on)->lessThan(now());
    }

    protected function getExpirationDatetime()
    {  
        return now()->addDays($this->withDuration()->license->duration->days());
    }

    public function daysLeft()
    {
        $expiresOn = $this->expires_on ?? $this->getExpirationDatetime(); 

        return now()->startOfDay()->diffInDays($expiresOn->startOfDay(), false);
    }

    public function startedAt()
    {
        if($this->inUse()) {  
            return $this->withDuration()->expires_on->subDays($this->license->duration->days());
        } 
    }

    public function withDuration()
    {
        $this->loadMissing([
            'license' => function($q) {
                $q->withTrashed()->with([
                    'duration' => function($q) {
                        $q->withTrashed();
                    }
                ]);
            }
        ]);

        return $this;
    }

    public function orders()
    {
        return $this->belongsToMany(\Armincms\Orderable\Models\Order::class, 'el_credit_order'); 
    }
}

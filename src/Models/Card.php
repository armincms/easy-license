<?php

namespace Armincms\EasyLicense\Models;

use Armincms\Contract\Concerns\Configurable;
use Armincms\Contract\Concerns\GeneratesTrackingCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use Configurable;
    use HasFactory;
    use GeneratesTrackingCode;
    use SoftDeletes;

    /**
     * Tracking code column name.
     *
     * @var string
     */
    const TRACKING_CODE = 'number';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enable' => 'boolean',
        'expires_on' => 'datetime',
        'activated_at' => 'datetime',
        'sold_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Query realted License.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    /**
     * Query realted User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Query where is 'enable'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
        return $query->whereEnable(true);
    }

    /**
     * Query where is 'enable'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeAvailables($query)
    {
        return $query->whereIsNull('sold_at');
    }

    /**
     * Query where delivery is 'available'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeNonExpires($query)
    {
        return $query->where('expires_on', '<', now());
    }

    /**
     * Check if card is available to sell.
     */
    public function isAvailable(): bool
    {
        return ! $this->isExpired() && ! $this->isSold();
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        return ! empty($this->expires_on) && $this->expires_on->lte(now());
    }

    /**
     * Check if card is sold.
     */
    public function isSold(): bool
    {
        return ! is_null($this->sold_at);
    }

    /**
     * Check if card is activated.
     */
    public function isActivated(): bool
    {
        return ! is_null($this->activated_at);
    }

    /**
     * Check if card is avaialbe to sell.
     */
    public function isEnable(): bool
    {
        return (bool) $this->enable;
    }
}

<?php

namespace Armincms\EasyLicense\Models;

use Armincms\Categorizable\HasCategories;
use Armincms\Contract\Concerns\Configurable;
use Armincms\Contract\Concerns\InteractsWithMedia;
use Armincms\Contract\Concerns\InteractsWithWidgets;
use Armincms\Contract\Contracts\HasMedia;
use Armincms\EasyLicense\Nova\License as NovaLicense;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model implements HasMedia
{
    use Configurable;
    use HasCategories;
    use HasFactory;
    use InteractsWithMedia;
    use InteractsWithWidgets;
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'array',
    ];

    /**
     * Query realted Cards.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Query realted Duration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duration()
    {
        return $this->belongsTo(\Armincms\Duration\Models\Duration::class);
    }

    /**
     * Query where is 'enable'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeEnables($query)
    {
        return $query->whereEnable(true);
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
     * Check if card is avaialbe to sell.
     */
    public function isEnable(): bool
    {
        return (bool) $this->enable;
    }

    /**
     * Query where delivery is 'card'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeCard($query)
    {
        return $query->whereDelivery('card');
    }

    /**
     * Query where delivery is 'manual'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeManual($query)
    {
        return $query->whereDelivery('manual');
    }

    /**
     * Query where delivery is 'system'.
     *
     * @param \Illuminate\Eloquent\Builder
     * @return \Illuminate\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->whereDelivery('system');
    }

    /**
     * Check the license delivery by card.
     *
     * @return bool
     */
    public function hasCard(): bool
    {
        return $this->delivery === 'card';
    }

    /**
     * Check the license delivery is manual.
     *
     * @return bool
     */
    public function isManual(): bool
    {
        return $this->delivery === 'manual';
    }

    /**
     * Check the license delivery by the system.
     *
     * @return bool
     */
    public function isAutomate(): bool
    {
        return $this->delivery === 'system';
    }

    /**
     * Generate data for license card.
     *
     * @return array
     */
    public function generateCardInformation(): array
    {
        $driver = NovaLicense::driver($this->config('operator'), $this->config('driver'));
        $generator = $driver['generator'] ?? fn () => [];

        return is_callable($generator) ? (array) call_user_func($generator, $this) : [];
    }

    /**
     * Get the license raw price.
     *
     * @return float
     */
    public function originalPrice(): float
    {
        return (float) $this->price;
    }

    /**
     * Get the discounted license price.
     *
     * @return float
     */
    public function finalPrice(): float
    {
        return $this->originalPrice();
    }

    /**
     * Get the discount amount.
     *
     * @return float
     */
    public function discountAmount(): float
    {
        return $this->originalPrice() > 0 ? $this->originalPrice() - $this->finalPrice() : 0;
    }

    /**
     * Get the discounted percentage.
     *
     * @return float
     */
    public function discountPercent(): float
    {
        return $this->originalPrice() > 0 ? ($this->discountAmount() / $this->originalPrice()) * 100 : 100;
    }

    /**
     * Serialize the model to pass into the client view.
     *
     * @param Zareismail\Cypress\Request\CypressRequest
     * @return array
     */
    public function serializeForDetailWidget($request)
    {
        return array_merge($this->getFirstMediasWithConversions()->toArray(), [
            'id' => $this->getKey(),
            'name' => $this->title(),
            'originalPrice' => $this->originalPrice(),
            'finalPrice' => $this->finalPrice(),
            'price' => $this->finalPrice(),
            'discountAmount' => $this->discountAmount(),
            'discountPercent' => $this->discountPercent(),
            'delivery' => $this->delivery,
            'users' => $this->users,
            'duration' => optional($this->duration)->title(),
        ]);
    }

    /**
     * Get the value that should be displayed to represent the model.
     *
     * @return string
     */
    public function title(): string
    {
        return (string) data_get($this, 'name.'.app()->getLocale()) ?? array_shift((array) $this->name);
    }
}

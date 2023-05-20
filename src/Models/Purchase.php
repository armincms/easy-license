<?php

namespace Armincms\EasyLicense\Models;

use Armincms\Arminpay\Contracts\Billable;
use Armincms\Arminpay\Models\ArminpayTransaction;
use Armincms\Contract\Concerns\GeneratesTrackingCode;
use Armincms\Contract\Concerns\HasDetail;
use Armincms\Contract\Concerns\InteractsWithWidgets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Zareismail\Gutenberg\Models\GutenbergFragment;

class Purchase extends Model implements Billable
{
    use GeneratesTrackingCode;
    use HasDetail;
    use HasFactory;
    use InteractsWithWidgets;

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
        'created_at' => 'datetime',
    ];

    /**
     * Query realted License.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseCheckout()
    {
        return $this->belongsTo(GutenbergFragment::class, 'gutenberg_fragment_id');
    }

    /**
     * Query realted License.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Query realted Card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
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
     * Query realted Transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->morphOne(ArminpayTransaction::class, 'billable')->where(fn($query) => $query->successed());
    }

    /**
     * Query realted Transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transactions()
    {
        return $this->morphMany(ArminpayTransaction::class, 'billable');
    }

    /**
     * The payment amount.
     *
     * @return float
     */
    public function billingAmount(): float
    {
        return $this->amount;
    }

    /**
     * The payment currency.
     *
     * @return float
     */
    public function billingCurrency(): string
    {
        return 'IRR';
    }

    /**
     * Return the path that should be called after the success payment.
     *
     * @return float
     */
    public function successCallback(): string
    {
        return route('el.purchase.done', $this->trackingCode());
    }

    /**
     * Return the path that should be called after the failed payment.
     *
     * @return float
     */
    public function failCallback(): string
    {
        return $this->checkoutPage();
    }

    /**
     * Return the path that should be called for invoicing.
     *
     * @return float
     */
    public function checkoutPage(): string
    {
        return $this->loadMissing('purchaseCheckout.website')->purchaseCheckout->getUrl($this->trackingCode());
    }

    /**
     * Serialize the model to pass into the client view.
     *
     * @param Zareismail\Cypress\Request\CypressRequest
     * @return array
     */
    public function serializeForDetailWidget($request)
    {
        return array_merge($this->license->serializeForDetailWidget($request), [
            'user' => $this->user->serializeForDetailWidget($request),
            'number' => $this->number,
            'count' => $this->count,
            'amount' => $this->amount,
        ]);
    }

}

<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{Badge, Text, Number, Password, Select, Boolean, BooleanGroup, BelongsTo, HasMany}; 
use Armincms\Orderable\Nova\Invoice as Resource; 
use Pdmfc\NovaFields\ActionButton;
use Dpsoft\NovaPersianDate\PersianDateTime;

class Order extends Resource
{ 
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Easy License';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [
        'saleables.saleable', 'customer', 'orderable', 'transactions'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    { 
        return [
            Text::make(__('Customer'), function($order) { 
                return is_null($order->customer) ? __('__') : $this->customerDetail($order->customer);
            })->asHtml(),

            Number::make(__('Total Price'), function() {
                return currency_format(
                    $this->saleables->sum->totalPrice(),  $this->saleables->first()['currency'] ?? 'IRR'
                );
            }),  

            Text::make(__('License'), function() { 
                $name = optional($this->orderable)->name;

                return request('resourceId') ? $name : Str::words($name, 3);
            }),

            Text::make(__('Count'), function() { 
                return $this->saleables->sum('count');
            })->sortable(),

            $this->when(! $this->shouldDelivery(), function() { 
                return  Badge::make(__('Status'), 'marked_as')->map([
                    'completed' => 'success',
                    'pending'   => 'warning', 
                    'draft'     => 'warning', 
                    'payment'   => 'info', 
                ])->sortable(); 
            }), 

            $this->when($this->shouldDelivery(), function() { 
                return  ActionButton::make(__('Status'))
                            ->action(Actions\Delivery::class, $this->id)
                            ->text(__('Delivered')); 
            }),              

            Text::make(__('Tracking Code'), 'tracking_code')
                ->sortable(),

            Text::make(__('Bank Reference'), function() {
                return optional($this->transactions->filter->isSuccessed()->pop())->referenceNumber();
            })->onlyOnDetail(),

            PersianDateTime::make(__('Created At'), function() {
                return $this->created_at->setTimezone('Asia/Tehran');
            }), 

            $this->when($request->isMethod('get'), function() {
                return new Panels\OrderDetail(__('Order Detail'), $this->resource);
            }),
        ];
    } 

    /**
     * Returns details of customer.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $customer 
     * @return string           
     */
    public function customerDetail($customer)
    {
        $fullname = $customer->firstname.PHP_EOL.$customer->lastname;

        $detail = '<div class="flex flex-col">';
            $detail .= '<div class="flex p-1"><h5>'.__('Name').': </h5>'; 
            $detail .= '<span class=text-info>'. (empty(trim($fullname))? $customer->username : $fullname). '</sapn>';
            $detail .= "</div>";
            $detail .= '<div class="flex p-1"><h5>'.__('Email').': </h5>'; 
            $detail .= '<span class=text-info>'.$customer->email. '</sapn>';
            $detail .= "</div>";
            $detail .= '<div class="flex p-1"><h5>'.__('Mobile').': </h5>'; 
            $detail .= '<span class=text-info>'.$customer->getMeta('mobile'). '</sapn>';
            $detail .= "</div>";
        $detail .= "</div>";

        return $detail;

    } 

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'easy-license-'. parent::uriKey();
    }

    /**
     * Get the actions available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function shouldDelivery()
    { 
        return $this->marked_as === 'completed' && 
                data_get($this->orderable, 'delivery') == 'manual' &&
                ! $this->isDelivered();
    }

    /**
     * Determine if delivery action run.
     * 
     * @return boolean 
     */
    public function isDelivered()
    {
        return once(function() {
            return forward_static_call([config('nova.actions.resource'), 'newModel'])->whereName('Delivery')->get()->pluck('model_id');
        })->contains($this->id);  
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }


    /**
     * Determine if the current user can update the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can delete the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToDelete(Request $request)
    {
        return false;
    }

    /**
     * Get the actions available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new Actions\Delivery)->canRun(function() {
                return true;
            }),
        ];
    }
}

		
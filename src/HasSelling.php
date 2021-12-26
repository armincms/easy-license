<?php

namespace Armincms\EasyLicense;
 

trait HasSelling 
{    
    /**
     * Mark the model with the "sale" value.
     *
     * @return $this
     */
    public function asSold(bool $sale = true)
    {
        return $this->markAs($this->getSaleValue($sale));
    } 

    /**
     * Determine if the value of the model's "marked as" attribute is equal to the "true" value.
     *  
     * @return bool       
     */
    public function isSold()
    {
        return $this->markedAs($this->getSaleValue());
    }

    /**
     * Determine if the value of the model's "marked as" attribute is equal to the "false" value.
     *  
     * @return bool       
     */
    public function forSale()
    {
        return ! $this->isSold();
    }

    /**
     * Query the model's `marked as` attribute with the "sale" value.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query 
     * @param  bool $sale  
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeSold($query, bool $sale = true)
    {
        return $query->mark($this->getSaleValue($sale));
    }

    /**
     * Query the model's `marked as` attribute with the "false" value.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query   
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeForSales($query)
    {
        return $query->sold(false);
    }

    /**
     * Set the value of the "marked as" attribute as "true" value.
     *
     * @param  bool $sale
     * @return $this
     */
    public function setSale(bool $sale = true)
    {
        return $this->setMarkedAs($this->getSaleValue($sale));
    } 

    /**
     * Get the value of the "sale" mark.
     *
     * @return string
     */
    public function getSaleValue(bool $sale = true)
    {
        return (int) $sale;
    }

    /**
     * Get the name of the "marked as" column.
     *
     * @return string
     */
    public function getMarkedAsColumn()
    {
        return 'sold';
    }
}

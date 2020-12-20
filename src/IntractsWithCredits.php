<?php

namespace Armincms\EasyLicense;

use Armincms\EasyLicense\Nova\Manufacturer;
use Armincms\EasyLicense\Credit;
  
trait IntractsWithCredits  
{  
	/**
	 * Create new credit.
	 * 
	 * @param  \Illuminate\Database\Eloqent\Model $user  
	 * @param  string $usage 
	 * @param  integer $count 
	 * @return \Illuminate\Support\Collection        
	 */
	public function createCredit($user, $usage, $count = 1)
	{ 
        return collect(range(1, $count))->map(function() use ($usage, $user) {
        	return tap($this->fillCredit($user, $usage), function($credit) {
        		$credit->save();
        	}); 
        })->values();
	}

	/**
	 * Fill new Credit model.
	 * 
	 * @param  \Illuminate\Database\Eloqent\Model $user  
	 * @param  string $usage 
	 * @return \Illuminate\Database\Eloqent\Model        
	 */
	public function fillCredit($user, string $usage)
	{
		return Credit::unguarded(function() use ($user, $usage) { 
			$attributes = [
                'license_id' => $this->id,
                'usage' => $usage,
                'data' => $this->fillCreditFields(),
            ];

            return tap(Credit::firstOrNew($attributes), function($credit) use ($user) {
            	$credit->user()->associate($user);
            });
        });
	}

	/**
	 * Fill the credit fields.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function fillCreditFields()
	{ 
        $data = call_user_func($this->getCreditBuilder());

		return collect($this->product->prepareFields())->flatMap(function($field) use ($data) {
            $name = $field['name'];

            return [
                $name => data_get($data, $name)
            ];
        })->toArray();
	}

	/**
	 * Get the builder callback.
	 * 
	 * @return callable
	 */
	public function getCreditBuilder()
	{ 
        $this->loadMissing(['product' => function($q) {
            $q->withTrashed()->with(['manufacturer' => function($q) {
                $q->withTrashed();
            }]);
        }]); 

        if(is_null($manufacturer = optional($this->product)->manufacturer)) {
            abort(404);
        }

        $manufacturer = new Manufacturer($manufacturer);

        return $manufacturer->drivers()->get($this->product->driver)['builder'] ?? function() {
          return [];
        }; 
	}
}

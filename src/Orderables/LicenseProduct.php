<?php 
namespace Armincms\EasyLicense\Orderables;

use Armincms\Arminpay\Contracts\Orderable;
use Armincms\Arminpay\Contracts\Product as ProductContract;
use Armincms\Arminpay\Product; 
use Armincms\EasyLicense\License; 
use Armincms\EasyLicense\Nova\EasyLicense; 

class LicenseProduct implements Orderable
{ 
	public function name(): string
	{
		return 'easy-license.product';
	}

	public function resolve($licenceId) : ProductContract
	{
		$license = License::find($licenceId);

		abort_if(is_null($license), 402, 'Invalid License');

		return new Product([
			'price'       => $license->price,
			'discount'    => $license->discountPrice(),
			'unit'        => EasyLicense::option('el_currency', 'IRR'),
			'title'       => $this->displayName($license),
			'description' => $license->description,
			'id'		  => $license->id, 
			"days"	  	  => optional($license->duration)->days(),
			"users"		  => $license->users,
		]); 
	}

	public function displayName($license)
	{
		return "{$license->title} | " .
                _("User Count") .": {$license->user_count} | ".
                _("Duration") .": ". optional($license->timing)->days()._("Day");
	}
}

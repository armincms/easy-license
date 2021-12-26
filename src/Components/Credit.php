<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document; 
use Armincms\EasyLicense\License;
use Armincms\EasyLicense\Manual;
use Armincms\Orderable\Models\Order;
use Armincms\Nova\User;

class Credit extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = 'order/{tracking_code}/credits';

	public function toHtml(Request $request, Document $docuemnt) : string
	{   
		$order = Order::viaCode($request->route('tracking_code'))->with('saleables', 'orderable', 'customer')->firstOrFail(); 

		$this->resource($order);  

		$docuemnt->title(data_get($order, 'seo.title') ?: $order->trackingCode()); 
		$docuemnt->description(
			data_get($order, 'seo.description') ?: mb_substr(strip_tags($order->trackingCode()), 0, 100) 
		); 

		return  $this->firstLayout($docuemnt, $this->config('layout'), 'clean-license-order')
					->display(array_merge($order->toArray(), ['count' => $request->get('count')]))
					->toHtml();  
	}   

	public function saleables()
	{
	 	return $this->resource->saleables;
	} 

	public function trackingCode()
	{
		return $this->resource->trackingCode();
	}

	public function credits()
	{
		if ($this->resource->orderable->delivery == 'card') {
			$manuals = $this->resource->saleables->flatMap->details->pluck('id'); 

			return Manual::withTrashed()->find($manuals->toArray());			
		}

		return $this->resource->orderable->credits()->whereHas('orders', function($query) {
			$query->whereKey($this->resource->getKey());
		})->get();
	}
}

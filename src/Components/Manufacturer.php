<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document;
use Armincms\EasyLicense\Manufacturer as Model;

class Manufacturer extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout;

	/**
	 * Route Conditions of section
	 * 
	 * @var null
	 */
	protected $wheres = [ 
		'id'	=> '[0-9]+'
	];   

	public function toHtml(Request $request, Document $docuemnt) : string
	{       
		$manufacturer = Model::whereHas('translations', function($query) use ($request) {
			$query->where('url', $request->relativeUrl());
		})->firstOrFail(); 

		$this->resource($manufacturer);  

		$docuemnt->title(data_get($manufacturer, 'seo.title') ?: $manufacturer->title); 
		$docuemnt->description(
			data_get($manufacturer, 'seo.description') ?: mb_substr(strip_tags($manufacturer->description), 0, 100) 
		); 

		return (string) $this
							->firstLayout($docuemnt, $this->config('layout'), 'manufacturer')
							->display($manufacturer->toArray(), $docuemnt->component->config('layout', [])); 
	}     

	public function products()
	{
		return $this->resource->load('products')->products->map(function($product) {
			return [
				'name' => $product->name,
				'url'  => $this->resource->site()->url("product/{$product->id}"),
			];
		});
	}
}

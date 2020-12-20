<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\HttpSite\Concerns\HasInternalLayout;
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
							->firstLayout($docuemnt, $this->config('layout'), 'clean-manufacturer')
							->display($manufacturer->toArray(), $docuemnt->component->config('layout', [])); 
	}     

	public function products()
	{
		return $this->resource->products()->paginate(6);
	}

	public function productInformation($product)
	{ 
		return [
            'id'    => $product->id,
            'name'  => $product->name,
            'image' => $product->featuredImages(),
            'url'   => $this->resource->site()->url("product/{$product->id}"),
            'features'  => $product->features(),
            'abstract'  => $product->abstract,
		]; 
	} 

	public function productLayout($docuemnt)
	{
		return $this->firstLayout($docuemnt, $this->config('layout.product'), 'clean-license-product-review');
	}
}

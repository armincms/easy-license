<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document;
use Armincms\EasyLicense\Product as Model;

class Product extends Component implements Resourceable
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
		$product = Model::findOrFail($request->route('id')); 

		$this->resource($product);  

		$docuemnt->title(data_get($product, 'seo.title') ?: $product->title); 
		$docuemnt->description(
			data_get($product, 'seo.description') ?: mb_substr(strip_tags($product->description), 0, 100) 
		); 

		return  $this
					->firstLayout($docuemnt, $this->config('layout'), 'product')
					->display($product->toArray(), $docuemnt->component->config('layout', []))
					->toHtml(); 
	}     

	public function licenses()
	{
		return $this->resource->load('licenses')->licenses;
	}
}

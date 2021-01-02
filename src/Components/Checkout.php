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

class Checkout extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	public function toHtml(Request $request, Document $docuemnt) : string
	{        
		$license = License::findOrFail($request->route('id'));    

		$this->resource($license);  

		$docuemnt->title(data_get($license, 'seo.title') ?: $license->name()); 
		$docuemnt->description(
			data_get($license, 'seo.description') ?: mb_substr(strip_tags($license->description()), 0, 100) 
		); 

		return  $this->firstLayout($docuemnt, $this->config('layout'), 'clean-license-checkout')
					->display(array_merge($license->toArray(), [
						'count' => $request->get('count'), 
			            'oldPrice'  => $license->oldPrice(),
			            'price'  	=> $license->salePrice(),
					]))
					->toHtml(); 
	} 

	public function method($value='')
    {
    	return 'any';
    }    
}

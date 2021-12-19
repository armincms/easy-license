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
use Armincms\Orderable\Models\Order as OrderableOrder;
use Armincms\Nova\User;

class Order extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	/**
	 * Route Conditions of Component.
	 * 
	 * @var null
	 */
	protected $middlewares = ['web']; 

	public function toHtml(Request $request, Document $docuemnt) : string
	{        
		$request->validate([
			'email' => 'required|email',
			'mobile' => 'required|numeric',
			'g-recaptcha-response' => 'required|captcha'
		]);

		$license = License::findOrFail($request->route('id'));    
		$user = $this->getUser($request);

		$order = \DB::transaction(function() use ($request, $license, $user) {
			$callback = function($order) use ($request, $license, $user) {
				// $order->orderable()->associate($license);
				// $order->customer()->associate($user);
				$order->save();
				$order->add($license, $request->count ?: 1);
				$order->loadMissing('saleables'); 

				$order->forceFill([
					'finish_callback' => app('site')->get('easy-license')->url('order/'.$order->trackingCode().'/credits')
				])->save();
			};

			return tap(OrderableOrder::createFromModel($license, $user), $callback);
		}); 

		return redirect(app('site')->get('orders')->url("{$order->trackingCode()}/billing"));
	}  

	public function getUser(Request $request)
	{  
		return tap(User::newModel()->updateOrCreate(['email' => $request->email],[
			'email' 	=> $request->email,
			'username' 	=> $request->mobile,
			'firstname'	=> $request->firstname,
			'lastname' 	=> $request->lastname,
			'password'	=> bcrypt($request->mobile),
		]), function($user) use ($request) {
			$user->setMeta('mobile', $request->mobile);
			$user->save();
		}); 

	}

	public function method()
	{
    	return 'post';
    }    
}

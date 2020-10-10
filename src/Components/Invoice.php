<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component as BaseComponent;  
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\HttpSite\Contracts\Resourceable;
use Core\Document\Document;
use Armincms\EasyLicense\Licence;
use Armincms\EasyLicense\Mails\OrderShipped; 
use Component\Arminpay\Order;

class Invoice extends BaseComponent implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout;
	/**
	 * Name of Component.
	 * 
	 * @var null
	 */
	protected $name = 'invoice';

	/**
	 * Label of Component.
	 * 
	 * @var null
	 */
	protected $label = 'licence-management::title.licence_orders';

	/**
	 * SingularLabe of Component.
	 * 
	 * @var null
	 */
	protected $singularLabel = 'licence-management::title.licence_order'; 

	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = 'order/{tracking_code}/invoice';

	/**
	 * Route Conditions of section
	 * 
	 * @var null
	 */
	protected $wheres = [ 
		'oreder'	=> '[0-9]+'
	];   

	public function toHtml(Request $request, Document $docuemnt) : string
	{       
		$this->checkSession($trackingCode = $request->route('tracking_code'));   

		$this->markOrderAsDelivered(
			$order = Order::with('transactions')->whereTrackingCode($trackingCode)->firstOrFail()
		);  

		$this->resource($order);  

		$docuemnt->title("Order {$trackingCode} Invoice"); 
		
		$docuemnt->description("Order {$trackingCode} Invoice");

		$this->sendMail();   

		return $this->firstLayout($docuemnt, $this->config('layout'), 'aqoela-license'/*'isotope'*/)
					->display(compact('order'), $docuemnt->component->config('layout', [])); 
	}   

	public function checkSession(string $trackingCode)
	{  
		app('arminpay.trace') === $trackingCode || abort(403, 'There Is Nothing Here !!!!');
	}

	/**
	 * Indicate order delivered.
	 * 
	 * @param  Order  $order 
	 * @return [type]        
	 */
	public function markOrderAsDelivered(Order $order)
	{  
		if(Order::SUCCESS !== $order->status) { 
			// if not builded order purcahses.  
			$order->update([
				'purchase_detail' => (array) $this->preparePurchase($order),
				'status' => Order::SUCCESS,
			]); 
		}  
	}

	public function explanations($positions = [])
	{
		if(! isset($this->explanations)) {
			$callback = function($q) {
				return $q->whereHas('licenses', function($q) {
					return $q->whereIn(
						$q->qualifyColumn('id'), 
						$this->retriveOrderedProducts($this->resource)->toArray()
					); 
				});
			};

			$this->explanations = Explanation::whereDoesntHave('products')
										->orWhereHas('products', $callback)->get();
		}

		return $this->explanations->filter(function($explanation) use ($positions) {
			return  empty((array) $positions) || 
					in_array($explanation->position, (array) $positions);
		});
	}

	public function sendMail()
	{  
		return \Mail::to($this->resource->customer)->send(new OrderShipped($this->resource));
	}  

	protected function preparePurchase(Order $order)
	{ 
		return $this->licences($order)->map([$this, 'getPurchaseData'])->filter()->toArray(); 
	} 

	public function licences(Order $order)
	{
		if(! isset($this->licences)) {
			$this->licences = $this->resolveLicences($order);
		}

		return $this->licences;
	}

    /**
     * Resolve ordered licences.
     * 
     * @param  string $orderable 
     * @param  array  $licences  
     * @return array            
     */
    public function resolveLicences($order) 
    {     
		return Licence::with(['card.data', 'product' => function($q) {
	    	$q->withTrashed()->with([
	    		'manufacturer' => function($q) { $q->withTrashed(); }
	    	]);
	    }])->find($this->retriveOrderedProducts($order))->filter();  
    } 


	public function retriveOrderedProducts(Order $order)
	{ 
		return $order->order_detail->map->jsonSerialize()->pluck('id'); 
	}

    public function getPurchaseData(Licence $licence)
    {
    	$data['id'] = $licence->id;

    	switch ($licence->delivery_method) {
    		case 'card':
    			return array_merge($data, (array) $this->retriveCardData($licence));
    			break;
    		case 'system':
    			return array_merge($data, (array) $this->buildLicenceData($licence));
    			break;
    		
    		default:
    			return [];
    			break;
    	}  
    }

    public function retriveCardData(Licence $licence)
    {
    	if($card = $licence->card) {
    		$data = $card->data->filter->isPublished()->first(); 

    		return tap((array) optional($data)->data, function() use ($data) { 
    			is_null($data) || $data->update(['status' => 'sold']);
    		});
    	}

    	return [];
    }

    public function buildLicenceData(Licence $licence)
    {
    	$operator= array_get($licence->product, 'manufacturer.operator');
    	$driver= array_get($licence->product, 'driver'); 
		$builder = config("licence-management.operators.{$operator}.drivers.{$driver}.builder");  

    	return is_callable($builder) ? call_user_func_array($builder, []) : []; 
    }
}

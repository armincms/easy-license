<?php 
namespace Armincms\EasyLicense\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document;
use Armincms\EasyLicense\{Product as Model, Nova\EasyLicense}; 

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
					->firstLayout($docuemnt, $this->config('layout'), 'clean-license-product')
					->display($product->toArray(), $docuemnt->component->config('layout', []))
					->toHtml(); 
	}  

	public function features()
	{
		return $this->resource->getFeatures();
	}  

	public function licenses()
	{
		return $this->resource->loadMissing('licenses.duration')->licenses;
	}

	public function licenseLayout($docuemnt)
	{
		return $this->firstLayout($docuemnt, $this->config('layout.license'), 'clean-license-review');
	}

	public function licenseInformation($license)
	{ 
		return [
            'id'    => $license->id,
            'name'  => $license->name(),
            'image' => $license->featuredImages(),  
            'duration'  => optional($license->duration)->label,
            'oldPrice'  => $license->oldPrice(),
            'price'  	=> $license->salePrice(),
		]; 
	} 

	public function currency()
	{
		return EasyLicense::option('el_currency', 'IRR');
	}

	public function countries()
	{
		return [ 
			"AF" => "Afghanistan",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BR" => "Brazil",
			"VG" => "British Virgin Islands",
			"BN" => "Brunei",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"BQ" => "Caribbean Netherlands",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CO" => "Colombia",
			"CG" => "Congo - Brazzaville",
			"CD" => "Congo - Kinshasa",
			"CR" => "Costa Rica",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CW" => "Curaçao",
			"CY" => "Cyprus",
			"CZ" => "Czechia",
			"CI" => "Cote D'ivoire",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"XK" => "Kosovo",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Laos",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macau",
			"MK" => "North Macedonia",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia",
			"MD" => "Moldova",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PS" => "Palestine",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RO" => "Romania",
			"RU" => "Russia",
			"RW" => "Rwanda",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SX" => "Sint Maarten",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"KR" => "South Korea",
			"SS" => "South Sudan",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"KN" => "Saint Kitts And Nevis",
			"LC" => "Saint Lucia",
			"MF" => "St. Martin",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and The Grenadines",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syria",
			"TW" => "Taiwan",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks And Caicos Islands",
			"VI" => "U.S. Virgin Islands",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Vietnam",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe",
			"AX" => "Åland Islands",
		];
	}
}

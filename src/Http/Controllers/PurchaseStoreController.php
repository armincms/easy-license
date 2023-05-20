<?php
namespace Armincms\EasyLicense\Http\Controllers;

use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\Contract\Nova\User;
use Armincms\EasyLicense\Models\License;
use Armincms\EasyLicense\Models\Purchase;
use Armincms\EasyLicense\Nova\LicenseCheckout;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Zareismail\Gutenberg\Models\GutenbergFragment;

class PurchaseStoreController extends Controller {
    public function __invoke(Request $request)
    {
        $license = License::with('duration')->find($request->route('licenseId'));
        $purchaseCheckout = GutenbergFragment::find($request->get('purchaseCheckout'));
        $request->validate($this->rules());
        $user = $this->firstOrCreateUser($request, $request->only($this->fields()));

        $purchase = Purchase::unguarded(fn () => new Purchase([
            'amount' => $license->finalPrice() * $request->input('count', 1),
            'count' => $request->input('count', 1),
            'marked_as' => 'checkout',
            'detail' => [
                'license' => $license->serializeForDetailWidget($request),
                'user'  => $user->profile,
            ],
        ]));

        $purchase->user()->associate($user);
        $purchase->license()->associate($license);
        $purchase->purchaseCheckout()->associate($purchaseCheckout);
        $purchase->save();

        if ($gateway = ArminpayGateway::find($request->input('gate'))) {
            return $gateway->checkout($request, $purchase);
        }

        return redirect($purchaseCheckout->getUrl($purchase->trackingCode()));
    }

    public function rules(): array
    {
        return collect($this->fields())
            ->filter(fn ($field) => LicenseCheckout::option($field))
            ->flip()
            ->map(fn ($val, $field) => 'required'. ($field === 'email' ? '|email': ''))
            ->toArray();
    }

    public function fields(): array
    {
        return ['email', 'username', 'firstname', 'lastname', 'mobile', 'phone'];
    }

    public function firstOrCreateUser($request, $fields)
    {
        $name = $fields['username'] ?? $fields['mobile'] ?? $fields['email'] ?? 'guest' . now();
        $email = $fields['email'] ??  "{$name}@example.com";

        return User::newModel()->updateOrCreate(compact('email'), [
            'password' => bcrypt(now()),
            'name' => $name,
            'metadata::firstname' => $fields['firstname'] ?? null,
            'metadata::lastname' => $fields['lastname'] ?? null,
            'metadata::phone' => $fields['phone'] ?? null,
            'metadata::mobile' => $fields['mobile'] ?? null,
        ]);
    }
}

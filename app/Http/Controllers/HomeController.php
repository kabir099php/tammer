<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Order;
use App\Models\Item;
use App\Models\Contact;
use App\Models\Store;
//use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\DataSetting;
use App\Models\AdminFeature;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Vendor;
use App\Models\AdminTestimonial;
use App\Models\CustomCurrency;
use Gregwar\Captcha\CaptchaBuilder;
use App\Models\AdminSpecialCriteria;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use App\Models\AdminPromotionalBanner;
use App\Models\SubscriptionTransaction;
use Illuminate\Support\Facades\Session;
use Mpdf\Mpdf; 
use Illuminate\Support\Facades\Log; 
//use Barryvdh\DomPDF\Facade\Pdf; // Use the Facade

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $datas =  DataSetting::with('translations', 'storage')->where('type', 'admin_landing_page')->get();
        $data = [];
        foreach ($datas as $key => $value) {
            if (count($value->translations) > 0) {
                $cred = [
                    $value->key => $value->translations[0]['value'],
                ];
                array_push($data, $cred);
            } else {
                $cred = [
                    $value->key => $value->value,
                ];
                array_push($data, $cred);
            }
            if (count($value->storage) > 0) {
                $cred = [
                    $value->key . '_storage' => $value->storage[0]['value'],
                ];
                array_push($data, $cred);
            } else {
                $cred = [
                    $value->key . '_storage' => 'public',
                ];
                array_push($data, $cred);
            }
        }
        $settings = [];
        foreach ($data as $single_data) {
            foreach ($single_data as $key => $single_value) {
                $settings[$key] = $single_value;
            }
        }

        // $settings =  DataSetting::with('translations')->where('type','admin_landing_page')->pluck('value','key')->toArray();
        $opening_time = BusinessSetting::where('key', 'opening_time')->first();
        $closing_time = BusinessSetting::where('key', 'closing_time')->first();
        $opening_day = BusinessSetting::where('key', 'opening_day')->first();
        $closing_day = BusinessSetting::where('key', 'closing_day')->first();
        $promotional_banners = AdminPromotionalBanner::where('status', 1)->get()->toArray();
        $features = AdminFeature::where('status', 1)->get()->toArray();
        $criterias = AdminSpecialCriteria::where('status', 1)->get();
        $testimonials = AdminTestimonial::where('status', 1)->get();

        $zones = Zone::where('status', 1)->get();
        $zones = self::zone_format($zones);

        $landing_data = [
            'fixed_header_title' => (isset($settings['fixed_header_title']))  ? $settings['fixed_header_title'] : null,
            'fixed_header_sub_title' => (isset($settings['fixed_header_sub_title']))  ? $settings['fixed_header_sub_title'] : null,
            'fixed_module_title' => (isset($settings['fixed_module_title']))  ? $settings['fixed_module_title'] : null,
            'fixed_module_sub_title' => (isset($settings['fixed_module_sub_title']))  ? $settings['fixed_module_sub_title'] : null,
            'fixed_referal_title' => (isset($settings['fixed_referal_title']))  ? $settings['fixed_referal_title'] : null,
            'fixed_referal_sub_title' => (isset($settings['fixed_referal_sub_title']))  ? $settings['fixed_referal_sub_title'] : null,
            'fixed_newsletter_title' => (isset($settings['fixed_newsletter_title']))  ? $settings['fixed_newsletter_title'] : null,
            'fixed_newsletter_sub_title' => (isset($settings['fixed_newsletter_sub_title']))  ? $settings['fixed_newsletter_sub_title'] : null,
            'fixed_footer_article_title' => (isset($settings['fixed_footer_article_title']))  ? $settings['fixed_footer_article_title'] : null,
            'feature_title' => (isset($settings['feature_title']))  ? $settings['feature_title'] : null,
            'feature_short_description' => (isset($settings['feature_short_description']))  ? $settings['feature_short_description'] : null,
            'earning_title' => (isset($settings['earning_title']))  ? $settings['earning_title'] : null,
            'earning_sub_title' => (isset($settings['earning_sub_title']))  ? $settings['earning_sub_title'] : null,
            'earning_seller_image' => (isset($settings['earning_seller_image']))  ? $settings['earning_seller_image'] : null,
            'earning_seller_image_storage' => (isset($settings['earning_seller_image_storage']))  ? $settings['earning_seller_image_storage'] : 'public',
            'earning_delivery_image' => (isset($settings['earning_delivery_image']))  ? $settings['earning_delivery_image'] : null,
            'earning_delivery_image_storage' => (isset($settings['earning_delivery_image_storage']))  ? $settings['earning_delivery_image_storage'] : 'public',
            'why_choose_title' => (isset($settings['why_choose_title']))  ? $settings['why_choose_title'] : null,
            'download_user_app_title' => (isset($settings['download_user_app_title']))  ? $settings['download_user_app_title'] : null,
            'download_user_app_sub_title' => (isset($settings['download_user_app_sub_title']))  ? $settings['download_user_app_sub_title'] : null,
            'download_user_app_image' => (isset($settings['download_user_app_image']))  ? $settings['download_user_app_image'] : null,
            'download_user_app_image_storage' => (isset($settings['download_user_app_image_storage']))  ? $settings['download_user_app_image_storage'] : 'public',
            'testimonial_title' => (isset($settings['testimonial_title']))  ? $settings['testimonial_title'] : null,
            'contact_us_title' => (isset($settings['contact_us_title']))  ? $settings['contact_us_title'] : null,
            'contact_us_sub_title' => (isset($settings['contact_us_sub_title']))  ? $settings['contact_us_sub_title'] : null,
            'contact_us_image' => (isset($settings['contact_us_image']))  ? $settings['contact_us_image'] : null,
            'contact_us_image_storage' => (isset($settings['contact_us_image_storage']))  ? $settings['contact_us_image_storage'] : 'public',
            'opening_time' => $opening_time ? $opening_time->value : null,
            'closing_time' => $closing_time ? $closing_time->value : null,
            'opening_day' => $opening_day ? $opening_day->value : null,
            'closing_day' => $closing_day ? $closing_day->value : null,
            'promotional_banners' => (isset($promotional_banners))  ? $promotional_banners : null,
            'features' => (isset($features))  ? $features : [],
            'criterias' => (isset($criterias))  ? $criterias : null,
            'testimonials' => (isset($testimonials))  ? $testimonials : null,

            'counter_section' => (isset($settings['counter_section']))  ? json_decode($settings['counter_section'], true) : null,
            'seller_app_earning_links' => (isset($settings['seller_app_earning_links']))  ? json_decode($settings['seller_app_earning_links'], true) : null,
            'dm_app_earning_links' => (isset($settings['dm_app_earning_links']))  ? json_decode($settings['dm_app_earning_links'], true) : null,
            'download_user_app_links' => (isset($settings['download_user_app_links']))  ? json_decode($settings['download_user_app_links'], true) : null,
            'fixed_link' => (isset($settings['fixed_link']))  ? json_decode($settings['fixed_link'], true) : null,

            'available_zone_status' => (int)((isset($settings['available_zone_status'])) ? $settings['available_zone_status'] : 0),
            'available_zone_title' => (isset($settings['available_zone_title'])) ? $settings['available_zone_title'] : null,
            'available_zone_short_description' => (isset($settings['available_zone_short_description'])) ? $settings['available_zone_short_description'] : null,
            'available_zone_image' => (isset($settings['available_zone_image'])) ? $settings['available_zone_image'] : null,
            'available_zone_image_full_url' => Helpers::get_full_url('available_zone_image', (isset($settings['available_zone_image'])) ? $settings['available_zone_image'] : null, (isset($settings['available_zone_image_storage'])) ? $settings['available_zone_image_storage'] : 'public'),
            'available_zone_list' => $zones,
        ];


        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        $new_user = request()?->new_user ?? null;

        if (isset($config) && $config) {

            return view('home', compact('landing_data', 'new_user'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    private function zone_format($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'display_name' => $item['display_name'] ? $item['display_name'] : $item['name'],
                'modules' => $item->modules->pluck('module_name')
            ];
        }
        $data = $storage;

        return $data;
    }

    public function terms_and_conditions(Request $request)
    {
        $data = self::get_settings('terms_and_conditions');
        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('terms-and-conditions', compact('data'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function about_us(Request $request)
    {
        $data = self::get_settings('about_us');
        $data_title = self::get_settings('about_title');

        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('about-us', compact('data', 'data_title'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function contact_us()
    {
        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        if (isset($config) && $config) {
            return view('contact-us', compact('custome_recaptcha'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function send_message(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $gResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret' => $secret_key,
                            'response' => $value,
                            'remoteip' => \request()->ip(),
                        ]);

                        if (!$gResponse->successful()) {
                            $fail(translate('ReCaptcha Failed'));
                        }
                    },
                ],
            ]);
        } else if (strtolower(session('six_captcha')) != strtolower($request->custome_recaptcha)) {
            Toastr::error(translate('messages.ReCAPTCHA Failed'));
            return back();
        }

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        Toastr::success('Message sent successfully!');
        return back();
    }

    public function privacy_policy(Request $request)
    {
        $data = self::get_settings('privacy_policy');

        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('privacy-policy', compact('data'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function refund_policy(Request $request)
    {
        $data = self::get_settings('refund_policy');
        $status = self::get_settings_status('refund_policy_status');
        abort_if($status == 0, 404);
        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('refund', compact('data'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function shipping_policy(Request $request)
    {
        $data = self::get_settings('shipping_policy');
        $status = self::get_settings_status('shipping_policy_status');

        abort_if($status == 0, 404);
        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('shipping-policy', compact('data'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public function cancelation(Request $request)
    {
        $data = self::get_settings('cancellation_policy');
        $status = self::get_settings_status('cancellation_policy_status');
        abort_if($status == 0, 404);
        $config = Helpers::get_business_settings('landing_page');
        $landing_integration_type = Helpers::get_business_data('landing_integration_type');
        $redirect_url = Helpers::get_business_data('landing_page_custom_url');

        if (isset($config) && $config) {
            return view('cancelation', compact('data'));
        } elseif ($landing_integration_type == 'file_upload' && File::exists('resources/views/layouts/landing/custom/index.blade.php')) {
            return view('layouts.landing.custom.index');
        } elseif ($landing_integration_type == 'url') {
            return redirect($redirect_url);
        } else {
            abort(404);
        }
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = DataSetting::where(['key' => $name])->first();
        return $data ? $data->value : '';
    }

    public static function get_settings_localization($name, $lang)
    {
        $data = DataSetting::withoutGlobalScope('translate')->with(['translations' => function ($query) use ($lang) {
            return $query->where('locale', $lang);
        }])->where(['key' => $name])->first();
        if ($data && count($data->translations) > 0) {
            $data = $data->translations[0]['value'];
        } else {
            $data = $data ? $data->value : '';
        }
        return $data;
    }

    public static function get_settings_status($name)
    {
        $data = DataSetting::where(['key' => $name])->first()?->value;
        return $data;
    }

    public function lang($local)
    {
        $direction = BusinessSetting::where('key', 'site_direction')->first();
        $direction = $direction->value ?? 'ltr';
        $language = BusinessSetting::where('key', 'system_language')->first();
        foreach (json_decode($language['value'], true) as $key => $data) {
            if ($data['code'] == $local) {
                $direction = isset($data['direction']) ? $data['direction'] : 'ltr';
            }
        }
        session()->forget('landing_language_settings');
        Helpers::landing_language_load();
        session()->put('landing_site_direction', $direction);
        session()->put('landing_local', $local);
        return redirect()->back();
    }


    public function subscription_invoice($id)
    {
        $id = base64_decode($id);
        $BusinessData = ['admin_commission', 'business_name', 'address', 'phone', 'logo', 'email_address'];
        $transaction = SubscriptionTransaction::with(['store.vendor', 'package:id,package_name,price'])->findOrFail($id);
        $BusinessData = BusinessSetting::whereIn('key', $BusinessData)->pluck('value', 'key');
        $logo = BusinessSetting::where('key', "logo")->first();
        $mpdf_view = View::make('subscription-invoice', compact('transaction', 'BusinessData', 'logo'));
        Helpers::gen_mpdf(view: $mpdf_view, file_prefix: 'Subscription', file_postfix: $id);
        return back();
    }
    public function order_invoice($id)
    {
        $id = base64_decode($id);
        $BusinessData = ['footer_text', 'email_address'];
        $order = Order::findOrFail($id);
        $BusinessData = BusinessSetting::whereIn('key', $BusinessData)->pluck('value', 'key');
        $logo = BusinessSetting::where('key', "logo")->first();
        $mpdf_view = View::make('order-invoice', compact('order', 'BusinessData', 'logo'));
        Helpers::gen_mpdf(view: $mpdf_view, file_prefix: 'OrderInvoice', file_postfix: $id);
        return back();
    }

    public function details(Request $request)
    {
        // QrCode::format('png')
        //         ->size(300)
        //         ->generate("https://trymajlis.com/checkout", public_path('qrcodes-mannual/qr-link'  . '.png'));

        $host = $request->getHost(); // e.g., subdomain.yourdomain.com
        $parts = explode('.', $host);

        // Assuming subdomain.domain.com
        $subdomain = $parts[0]; 
        
        if($subdomain)
        {
            $store = Store::where('slug',$subdomain)->first();
            $vendor= Vendor::find($store->vendor_id);
            
            $currency = CustomCurrency::find($vendor->currency_id)->ar_code;
            Session::put("currencyAR",$currency) ; 
        }
        else
        {
            $store = Store::where('id',1)->first();
            $vendor= Vendor::find($store->vendor_id);
            
            $currency = CustomCurrency::find($vendor->currency_id)->ar_code;
            Session::put("currencyAR",$currency) ; 
        }
        
        
        return view('new_code.details' ,compact('store','currency'));
    }

    public function details1(Request $request)
    {
        // QrCode::format('png')
        //         ->size(300)
        //         ->generate("https://trymajlis.com/checkout", public_path('qrcodes-mannual/qr-link'  . '.png'));

        $host = $request->getHost(); // e.g., subdomain.yourdomain.com
        $parts = explode('.', $host);

        // Assuming subdomain.domain.com
        $subdomain = $parts[0]; 
        
        if($subdomain)
        {
            $store = Store::where('slug',$subdomain)->first();
            
            $vendor= Vendor::find($store->vendor_id);
            
            $currency = CustomCurrency::find($vendor->currency_id)->ar_code;
            Session::put("currencyAR",$currency) ; 
        }
        else
        {
            $store = Store::where('id',1)->first();
            $vendor= Vendor::find($store->vendor_id);
            
            $currency = CustomCurrency::find($vendor->currency_id)->ar_code;
            Session::put("currencyAR",$currency) ; 
        }
        
        
        return view('new_code.details' ,compact('store','currency'));
    }
    public function barcode()
    {
        Session::flush();
        return view('new_code.barcode');
    }
    public function barcodeFinal()
    {
        return view('new_code.barcode-final');
    }

    public function checkoutVendor($branch)
    {
        
        return view('new_code.checkout');
    }
    public function checkout(Request $request)
    {
        $store_id = $request->store_id ? $request->store_id  : 5 ; 
        $store = Store::find($store_id);
        $vendor= Vendor::find($store->vendor_id);
        
        $currency = CustomCurrency::find($vendor->currency_id)->ar_code;
        Session::put("currencyAR",$currency) ; 
        Session::put("vendor",$vendor) ; 
        Session::put("store",$store) ; 
        return view('new_code.checkout',compact('store_id','currency'));
    }

    public function payment()
    {
    
        $checkoutIteamsData  = Session::get('checkout_data');
        $currencyAR  = Session::get('currencyAR');
        $checkoutItems = $checkoutIteamsData['items'];
        $overallTotal = $checkoutIteamsData['total'];
        $vendor  = Session::get('vendor');
        $store  = Session::get('store');
        return view('new_code.payment' , compact('checkoutItems','overallTotal','currencyAR','vendor','store'));
    }
    public function processCheckout(Request $request)
    {
        

        $selectedItems = [];
        $totalPrice = 0;

        foreach ($request->input('items') as $item) {
                
            $productname = $item['id'];
            $quantity = (int) $item['quantity'];

                if(isset($item['type']) &&  $item['type'] == "scanner")
                {
                    $item_from_db = Item::where('id',$item['id'])->first();
                }
                else{
                    $item_from_db = Item::where('id',$item['id'])->first();
                }
                
                $actualPricePerKg = $item['price_per_kg'];
                $productName = $item['name'];

                $itemTotal = $actualPricePerKg * $quantity;
                $totalPrice += $itemTotal;

                $selectedItems[] = [
                    'id'=>$item_from_db->id,
                    'name' => $productName,
                    'quantity' => $quantity,
                    'price_per_kg' => $actualPricePerKg,
                    'total_item_price' => round($itemTotal, 2),
                    
                ];

            // // Server-side validation and price lookup (CRUCIAL for security)
            // if (isset($this->products[$productname])) {
            //     $productDetails = $this->products[$productname];
            //     $actualPricePerKg = $productDetails['price_per_kg'];
            //     $productName = $productDetails['name'];

            //     $itemTotal = $actualPricePerKg * $quantity;
            //     $totalPrice += $itemTotal;

            //     $selectedItems[] = [
                    
            //         'name' => $productName,
            //         'quantity' => $quantity,
            //         'price_per_kg' => $actualPricePerKg,
            //         'total_item_price' => round($itemTotal, 2)
            //     ];
            // } else {
            //     // Handle case where product ID is not found (e.g., return error)
            //     return response()->json(['message' => 'Invalid product selected: ' . $productId], 400);
            // }
        }

        // Store selected items and total in session
        Session::put('checkout_data', [
            'items' => $selectedItems,
            'total' => round($totalPrice, 2),
            'lang' => $request->input('lang')
        ]);

        return response()->json(['redirect_url' => route('payment')]);
    }
 public function subdomain ()
 {
    //dd("jj");
 }

 public function scanAndAddToCart(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $scannedBarcode = $request->input('barcode');

        // 2. Find the item by barcode in your 'items' table
        //    Make sure your Item model points to the correct table and has a 'barcode' field.
        $item = Item::where('barcode', $scannedBarcode)->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found for the given barcode.',
            ], 404); // 404 Not Found
        }

        // 3. Retrieve the current cart from the session
        //    The cart will be an associative array: ['item_id' => ['item_data', 'quantity']]
        $cart = Session::get('cart', []);
        $totalPrice = Session::get('cart_total_price', 0);

        // 4. Add or update the item in the cart
        //    If the item is already in the cart, increment its quantity.
        //    Otherwise, add it with a quantity of 1.
        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity']++;
            // Update total price: remove old price of this item and add new total price for it
            $totalPrice -= ($cart[$item->id]['item']->price * ($cart[$item->id]['quantity'] - 1)); // Subtract previous quantity's price
            $totalPrice += ($item->price * $cart[$item->id]['quantity']); // Add new quantity's price
        } else {
            $cart[$item->id] = [
                'item' => $item,
                'quantity' => 1
            ];
            $totalPrice += $item->price; // Add price of the new item
        }

        // 5. Store the updated cart and total sum back in the session
        Session::put('cart', $cart);
        Session::put('cart_total_price', $totalPrice);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully!',
            'item' => $item,
            'cart_count' => count($cart), // Number of unique items in cart
            'total_items_in_cart' => array_sum(array_column($cart, 'quantity')), // Total quantity of all items
            'cart_items' => array_values($cart), // Convert associative array to indexed array for easier JS iteration
            'cart_total_price' => $totalPrice,
        ]);
    }

    public function showCheckout()
    {
        $cart = Session::get('cart', []);
        $overallTotal = Session::get('cart_total_price', 0);

        // Transform cart data into the format expected by the Blade view
        // Your payment page expects an array like:
        // [
        //     ['name' => 'Product A', 'quantity' => 2, 'price_per_kg' => 10.50, 'total_item_price' => 21.00],
        //     ['name' => 'Product B', 'quantity' => 1, 'price_per_kg' => 5.00, 'total_item_price' => 5.00],
        // ]
        $checkoutItems = [];
        foreach ($cart as $itemId => $cartItem) {
            // Ensure 'item' is the actual Item model instance stored by BarcodeController
            $itemData = $cartItem['item'];
            $quantity = $cartItem['quantity'];

            // Safely access properties, provide defaults if necessary
            $itemName = $itemData->name ?? 'Unknown Product';
            $itemPrice = $itemData->price ?? 0;

            $checkoutItems[] = [
                'id' => $itemData->id ?? null, // Include ID if needed
                'name' => $itemName,
                'quantity' => $quantity,
                'price_per_kg' => $itemPrice,
                'total_item_price' => $itemPrice * $quantity,
            ];
        }

        // Determine current language dynamically if your application supports it.
        // For this example, assuming 'ar' as per your provided payment page's body class.
        // In a real app, you might use: app()->getLocale() or a language switcher.
        $currentLang = 'ar';

        return view('new_code.checkout-scanner', compact('checkoutItems', 'overallTotal', 'currentLang'));
    }
    public function paymentProcessing(Request $request)
    {        
        $order_id = $request->order_id;
        
        return view('new_code.payment_processing', ['order_id' => $order_id]);
    }

    public function thankyou ( Request $request)
    {        
        $order  = Order::find($request->order_id);

        
        return view('new_code.thankyou',compact('order'));
    }
    public function fail ( Request $request)
    {        
        $order  = Order::find($request->order_id);
        
        return view('new_code.fail');
    }

    public function checkOrderStatus (Request $request , $order_id )
    {
        
        $order = Order::where('id', $order_id)->first();

        if (!$order) {
            return response()->json(['status' => 'not_found', 'message' => 'Order not found'], 404);
        }

        // Return the current status to the frontend
        return response()->json($order);
    }

    public function invoice (Request $request , $id )
    {
        
        $order = Order::where(['id' => $id])->first();
        $this->downloadInvoice($order) ; 
        return view('new_code.invoice', compact('order'));

        
    }
    public function downloadInvoice(Order $order)
    {
        Log::info("Starting PDF generation for Order: " . $order->id);
        $vendor = Vendor::find($order->store->vendor_id);
        $data = [
            'order' => $order,
            'vendor' =>$vendor,
            // Any other data needed in your invoice Blade.
            // Ensure helper functions like \App\CentralLogics\Helpers::format_currency
            // and \App\Models\BusinessSetting are correctly accessible or mocked if needed.
        ];

        // Ensure this Blade file is actually empty or just has minimal HTML for this test
        $html = view('new_code.invoice', $data)->render();
        Log::info("Rendered HTML length: " . strlen($html));

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf/temp'), // Ensure this directory is writable by your web server
            'debug' => true, // Keep this for more mPDF internal logging
        ]);

        try {
            $mpdf->WriteHTML($html);
            Log::info("mPDF WriteHTML completed.");

            // ====================================================================
            // === CRITICAL DEBUGGING STEP: DIRECT mPDF OUTPUT AND EXIT ===
            // ====================================================================

            $filename = 'invoice-' . $order->id . '.pdf';
            $mpdf->Output($filename, 'D'); // 'D' forces download
            Log::info("mPDF Output 'D' called and script exited."); // This log should appear if Output() starts
            exit; // Stop all further Laravel execution

            // ====================================================================
            // === DO NOT UNCOMMENT THE BELOW `return response()->streamDownload`
            // === UNTIL THE ABOVE `mPDF->Output('D')` WORKS FLAWLESSLY.
            // ====================================================================

            /*
            return response()->streamDownload(function () use ($mpdf, $order) {
                Log::info("Starting streamDownload callback for Order: " . $order->id);
                echo $mpdf->Output('invoice-' . $order->id . '.pdf', 'S');
                Log::info("Finished streamDownload callback for Order: " . $order->id);
            }, 'invoice-' . $order->id . '.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice-' . $order->id . '.pdf"',
            ]);
            */

        } catch (\Mpdf\MpdfException $e) {
            Log::error("mPDF Exception for Order " . $order->id . ": " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return response('Error generating PDF: ' . $e->getMessage(), 500); // Return error message
        } catch (\Throwable $e) { // Catch all other exceptions (including Fatal Errors if possible)
            Log::error("General Exception during PDF generation for Order " . $order->id . ": " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return response('A server error occurred during PDF generation: ' . $e->getMessage(), 500); // Return error message
        }
    }
    
}

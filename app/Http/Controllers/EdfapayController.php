<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Support\Facades\Redirect; // For direct redirects if needed
use Illuminate\Support\Facades\DB; // For database transactions
use App\Models\Order;

class EdfapayController extends Controller
{
    /**
     * Initiate a payment request with Edfapay.
     * This method will be called by your frontend when the user clicks "Pay Now".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function initiatePayment(Request $request)
    {
     
        // 1. Validate incoming request data from your frontend
        $request->validate([
            'order_id' => 'required|string|max:255',
            'order_amount' => 'required|numeric|min:0.01',
            'order_currency' => 'required|string|size:3', // e.g., SAR, USD
            'order_description' => 'nullable|string|max:255',
            // Payer details are now explicitly required by the new API call
            'payer_first_name' => 'required|string|max:255',
            'payer_last_name' => 'required|string|max:255',
            'payer_address' => 'nullable|string|max:255', // Assuming address can be nullable
            'payer_country' => 'required|string|size:2', // e.g., SA for Saudi Arabia
            'payer_city' => 'required|string|max:255',
            'payer_zip' => 'nullable|string|max:20',
            'payer_email' => 'required|email|max:255',
            'payer_phone' => 'required|string|max:20', // Ensure this matches Edfapay's format (e.g., country code included)
            'payer_ip' => 'nullable|ip', // Auto-detect if not provided by frontend
            'term_url_3ds' => 'required|url|max:255', // URL for 3DS redirection
        ]);
        
        try {   
            // Retrieve credentials from config file (which gets them from .env)
            $merchantId = config('edfapay.merchant_id');
            $apiBaseUrl = config('edfapay.api_base_url'); // Should be 'https://api.edfapay.com'
            $merchantPassword = config('edfapay.merchant_password');

            // Ensure merchant ID and password are set
            if (empty($merchantId)) {
                Log::error('Edfapay MERCHANT_ID not set in environment variables.');
                return response()->json(['message' => 'Payment gateway not configured correctly.'], 500);
            }
            if (empty($merchantPassword)) {
                Log::error('Edfapay MERCHANT_PASSWORD not set in environment variables.');
                return response()->json(['message' => 'Payment gateway not configured correctly.'], 500);
            }
            
            // Prepare the data payload as FormData
            $formData = [
                'action' => 'SALE', // For immediate authorization and capture
                'edfa_merchant_id' => $merchantId,
                'order_id' => $request->input('order_id'),
                'order_amount' => number_format($request->input('order_amount'), 2, '.', ''), // Ensure 2 decimal places
                'order_currency' => strtoupper($request->input('order_currency')),
                'order_description' => $request->input('order_description', 'Online Purchase'),
                'req_token' => 'N', // 'Y' if you want to request a token for recurring payments
                'payer_first_name' => $request->input('payer_first_name'),
                'payer_last_name' => $request->input('payer_last_name'),
                'payer_address' => $request->input('payer_address'),
                'payer_country' => strtoupper($request->input('payer_country')),
                'payer_city' => $request->input('payer_city'),
                'payer_zip' => $request->input('payer_zip'),
                'payer_email' => $request->input('payer_email'),
                'payer_phone' => $request->input('payer_phone'),
                'payer_ip' => $request->input('payer_ip', $request->ip()), // Use request IP if not provided
                'term_url_3ds' => $request->input('term_url_3ds'),
                'auth' => 'N', // As per curl example
                'recurring_init' => 'N', // As per curl example
            ];

            // Calculate the hash as per Edfapay's new formula: sha1(md5(strtoupper(id.order.amount.currency.description.PASSWORD)))
            // Note: The example uses 'id' which maps to edfa_merchant_id, 'order' to order_id, etc.
            
            $hashString =  $formData['order_id'] .
                          $formData['order_amount'] .
                          $formData['order_currency'] .
                          $formData['order_description'] .
                          $merchantPassword;

            $formData['hash'] = sha1(md5(strtoupper($hashString)));
            session()->put('payment_hash', $formData['hash']);
            Log::info('Sending Edfapay Initiate Request:', $formData);

            // Make the POST request to Edfapay's initiate endpoint
            // The new curl URL specifies /payment/initiate
            $response = Http::asForm()->withoutRedirecting()->post("{$apiBaseUrl}/payment/initiate", $formData);
            
            Log::info('Edfapay Initiate Response:', [
                'status' => $response->status(),
                'headers' => $response->headers(), // Log headers to see Location
                'body' => $response->body(), // Raw body for debugging non-JSON errors
                'json_attempt' => $response->json(), // Will be null if not JSON
            ]);

            // Handle the Edfapay response
            
            if ($response->status() >= 300 && $response->status() < 400) {
                
                // This is a redirect response (301, 302, 303, etc.)
                $redirectUrl = $response->header('Location');
                if ($redirectUrl) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $redirectUrl,
                        'message' => 'Redirecting to payment gateway.'
                    ]);
                } else {
                    Log::error('Edfapay returned redirect status but no Location header.', [
                        'order_id' => $request->input('order_id'),
                        'status' => $response->status(),
                        'response_headers' => $response->headers(),
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment initiation failed: Invalid redirect response from gateway.'
                    ], 500);
                }
            } elseif ($response->successful()) {
                
                // HTTP status is 2xx, let's try to parse JSON
                
                $responseData = $response->json() ;
                Log::info(' Edfapay Initiate resopnse success:', $response->json());
                if ($responseData && isset($responseData['redirect_url']) ) {
                    // Edfapay wants to redirect the user to their hosted payment page
                    
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $responseData['redirect_url'],
                        'message' => 'Redirecting to payment gateway.'
                    ]);
                } elseif ($responseData && $responseData['result'] === 'SUCCESS') {
                    // Direct success (less common for hosted checkout without redirect first)
                    // This scenario is rare for a "Checkout" API, usually a redirect happens.
                    // This might imply an instant payment method or pre-authorized token use.
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment initiated successfully, awaiting callback.',
                        'transaction_id' => $responseData['trans_id'] ?? null
                    ]);
                } else {
                    // Edfapay returned a successful HTTP status but a non-success/redirect result,
                    // or the JSON structure was not as expected.
                    $errorMessage = $responseData['decline_reason'] ?? 'Payment initiation failed.';
                    Log::error('Edfapay initiate payment failed with non-redirect/non-success result or unexpected JSON.', [
                        'order_id' => $request->input('order_id'),
                        'response' => $responseData ?? $response->body() // Log full response if JSON parse failed
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'details' => $responseData
                    ], 400); // Bad Request
                }
            } else {
                // HTTP request itself failed (e.g., 4xx or 5xx status from Edfapay)
                $errorMessage = 'Edfapay API error.';
                if ($response->json() && isset($response->json()['message'])) {
                    $errorMessage = $response->json()['message'];
                } else {
                    // If not JSON, use the raw body for error message if it's human-readable
                    $errorMessage = 'Edfapay API request failed: ' . substr($response->body(), 0, 255) . '...';
                }
                Log::error('Edfapay API request failed with non-2xx status.', [
                    'order_id' => $request->input('order_id'),
                    'status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Error initiating Edfapay payment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'An internal server error occurred.'], 500);
        }
    }

    /**
     * Handle Edfapay callback notifications.
     * Edfapay will POST transaction results to this URL.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleCallback(Request $request)
    {
        Log::info('Edfapay Callback Received:', $request->all());

        if($request->result =="SUCCESS" &&  $request->status =="SETTLED" )
        {
            $order = Order::find($request->order_id)->update([
                 'payment_token' => $request->trans_id,
            'payment_type' => "WEB",
            'payment_status'=>"paid",
            'order_status'=>"delivered",
            'payement_gateway_status'=>"SUCCESS"
        ]);
        }
        if($request->result =="DECLINED" )
        {
            $order = Order::find($request->order_id)->update([
                 'payment_token' => $request->trans_id,
            'payement_gateway_status'=>"DECLINED"
        ]);
        }
        return response()->json($request->all());
        // 1. Validate the callback hash (CRITICAL for security)
        try {

            $merchantPassword = config('edfapay.merchant_password');
            $receivedHash = $request->input('hash');
            $transId = $request->input('trans_id');
            $orderId = $request->input('order_id');
            $result = $request->input('result'); // e.g., 'SUCCESS', 'DECLINED'
            $status = $request->input('status'); // e.g., 'SETTLED', 'DECLINED'
            $amount = $request->input('amount');
            $currency = $request->input('currency');

            // !!! IMPORTANT: YOU MUST CONFIRM THIS HASH CALCULATION WITH EDFAPAY'S OFFICIAL DOCUMENTATION !!!
            // The example in your comments md5(strtoupper(strrev(email).PASSWORD.trans_id.strrev(substr(card_number,0,6).substr(card_number,-4))))
            // is very specific and may not apply to all callback types or include all parameters.
            // A more common hash for simple sale callbacks might involve trans_id, order_id, amount, currency, and password.
            // For now, I'm using a common placeholder pattern. REPLACE WITH EDFAPAY'S EXACT FORMULA.

            $stringToHash = "{$transId}|{$orderId}|{$amount}|{$currency}|{$result}|{$status}|{$merchantPassword}";
            // Apply Edfapay's specific string manipulations (e.g., strtoupper, strrev) if required.
            // Example based on your original comment structure for success:
            // if ($result === 'SUCCESS') {
            //     $stringToHash = strtoupper(strrev($transId) . $merchantPassword . strrev($orderId));
            // } else {
            //     // Handle hash for DECLINED or other results based on EDFAPAY's docs
            //     $stringToHash = strtoupper(strrev($transId) . $merchantPassword . $result);
            // }

            $calculatedHash = md5($stringToHash);

            if ($calculatedHash !== $receivedHash) {
                Log::warning('Edfapay Callback SECURITY ALERT: Hash mismatch detected!', [
                    'order_id' => $orderId,
                    'trans_id' => $transId,
                    'received_hash' => $receivedHash,
                    'calculated_hash' => $calculatedHash,
                    'callback_data' => $request->all(),
                    // 'hash_string_used' => $stringToHash, // Only log this in dev/test, not production!
                ]);
                return response('Forbidden: Invalid Signature', 403); // Forbidden
            }

            // 2. Process the payment result (Idempotency and Database Transactions are key here)
            DB::transaction(function () use ($orderId, $transId, $request, $result, $status) {
                // Assuming you have an Order model:
                // use App\Models\Order;
                // $order = Order::where('order_id', $orderId)->first();

                // if (!$order) {
                //     Log::warning('Edfapay Callback: Order not found for ID', ['order_id' => $orderId]);
                //     // For callbacks on non-existent orders, returning OK might be safer after logging
                //     // to avoid gateway retries, but it indicates a system inconsistency.
                //     return;
                // }

                // // Idempotency check: Prevent reprocessing completed orders
                // if ($order->status === 'completed') {
                //     Log::info('Edfapay Callback: Order already completed, ignoring duplicate callback.', ['order_id' => $orderId]);
                //     return;
                // }

                if ($result === 'SUCCESS' && $status === 'SETTLED') {
                    // Payment was successful and settled
                    Log::info('Edfapay Callback: Payment successful and settled.', [
                        'order_id' => $orderId,
                        'trans_id' => $transId,
                        'amount' => $request->input('amount')
                    ]);
                    // Update your order status in your database to 'completed'
                    // $order->status = 'completed';
                    // $order->transaction_id = $transId;
                    // $order->payment_details = $request->all(); // Store full callback for audit
                    // $order->save();

                    // // Dispatch events or jobs for post-payment actions (e.g., send confirmation email)
                    // event(new OrderPaymentSucceeded($order));

                } elseif ($result === 'DECLINED' || $status === 'DECLINED') {
                    // Payment was declined
                    Log::warning('Edfapay Callback: Payment declined.', [
                        'order_id' => $orderId,
                        'trans_id' => $transId,
                        'decline_reason' => $request->input('decline_reason')
                    ]);
                    // Update your order status in your database to 'failed' or 'declined'
                    // $order->status = 'failed';
                    // $order->decline_reason = $request->input('decline_reason');
                    // $order->payment_details = $request->all(); // Store full callback for audit
                    // $order->save();

                    // // Dispatch events or jobs for failed payment actions
                    // event(new OrderPaymentFailed($order));

                } else {
                    // Handle other statuses like PENDING, etc.
                    Log::info('Edfapay Callback: Payment has non-final status.', [
                        'order_id' => $orderId,
                        'trans_id' => $transId,
                        'result' => $result,
                        'status' => $status
                    ]);
                    // $order->status = 'pending'; // Example
                    // $order->payment_details = $request->all();
                    // $order->save();
                }
            }); // End DB::transaction()

        } catch (\Exception $e) {
            Log::error('Error processing Edfapay callback:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'callback_data' => $request->all()
            ]);
            return response('Error', 500); // Internal Server Error
        }

        // Edfapay expects a 200 OK response to confirm receipt of the callback
        return response('OK', 200);
    }

    // Error logs
    public function storeErrorLogs(Request $request)
    {
        // Log the JSON data received from the frontend
        Log::info('Frontend Error: ');
        Log::info('Frontend Error: ',$request->all());

        return response()->json(['status' => 'success'], 200);
    }


     public function checkPaymentStatus($orderId)
    {
        Log::info('check status : ' , $orderId);
        // Merchant details from your .env file
        $order = Order::find($orderId);
        $merchantId = config('edfapay.merchant_id');
        $merchantSecret = config('edfapay.merchant_password');

        
        $hash = session()->get('payment_hash');

        $payload = [
            'order_id' => $orderId,
            'merchant_id' => $merchantId,
            "payer_ip"=> "176.44.76.222",
            'hash' => $hash,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://api.edfapay.com/payment/status', $payload);

            // Log the request and response for debugging
            Log::info('EdfaPay Status Check Request:', $payload);
            Log::info('EdfaPay Status Check Response:', ['status' => $response->status(), 'body' => $response->json()]);

            // Return the response body as an associative array
            return $response->json();

        } catch (\Exception $e) {
            // Log any exceptions that occur during the API call
            Log::error('EdfaPay API Call Failed:', [
                'exception' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            // Return an error response
            return [
                'error' => 'Failed to connect to the payment gateway.',
                'message' => $e->getMessage()
            ];
        }
    }
}

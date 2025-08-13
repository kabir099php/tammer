<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        /* Base styles for mPDF */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; /* A more common and readable sans-serif font for PDFs */
            margin: 0;
            padding: 30px; /* Increased padding for more white space around the edges */
            font-size: 10.5px; /* Consistent base font size */
            line-height: 1.5; /* Improved line spacing for readability */
            color: #333; /* Darker text for better contrast */
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-uppercase { text-transform: uppercase; }
        .text-break { word-break: break-word; }
        .font-weight-bold { font-weight: bold; } /* Explicitly bold for emphasis */
        .color-primary { color: #007bff; } /* Example primary color for accents */
        .color-secondary { color: #6c757d; } /* Example secondary color */

        /* Layout improvements using traditional CSS for mPDF compatibility */
        .container {
            width: 100%;
            margin: 0 auto;
            max-width: 700px; /* Constrain width for better readability on larger screens */
        }
        .row {
            clear: both; /* Ensures elements after floats start on a new line */
            overflow: hidden; /* Contains floats within the parent */
            margin-bottom: 15px; /* Add some space between rows */
        }
        .col-50 {
            width: 49%; /* Adjusted for a small gap in the middle */
            float: left;
            box-sizing: border-box;
            padding: 0 1%; /* Add horizontal padding within columns */
        }
        .col-100 {
            width: 100%;
            clear: both;
        }

        /* Header section */
        .invoice-header {
            margin-bottom: 30px;
            padding-bottom: 20px; /* More padding below header content */
            border-bottom: 1px solid #ddd; /* Clearer separator */
            text-align: center;
        }
        .invoice-logo {
            max-width: 130px; /* Slightly larger logo for prominence */
            margin-bottom: 15px; /* More space below logo */
        }
        .store-name {
            font-size: 2.2em; /* Larger and more prominent */
            margin-bottom: 8px;
            color: #222;
        }
        .store-address, .store-phone {
            font-size: 0.95em; /* Slightly larger for readability */
            color: #555;
            margin-top: 5px;
        }

        /* Receipt banner */
        .receipt-banner {
            margin: 25px 0; /* Increased vertical margin */
            background-color: #f0f8ff; /* Lighter, more inviting background */
            padding: 12px 0; /* More vertical padding */
            border-top: 1px solid #cceeff; /* Matching border color */
            border-bottom: 1px solid #cceeff;
            text-align: center;
        }
        .receipt-banner img {
            display: none; /* Keep decorative stars hidden for cleaner PDF */
        }
        .receipt-text {
            font-size: 1.3em; /* Slightly larger and bolder */
            font-weight: bold;
            color: #0056b3; /* Color that matches the background */
            letter-spacing: 1.5px; /* Increased letter spacing for emphasis */
        }

        /* Order Info */
        .order-meta-info {
            margin-bottom: 25px; /* More space below meta info */
            font-size: 0.98em; /* Slightly larger for clarity */
            padding: 15px; /* Add padding to give it a block feel */
            background-color: #fcfcfc; /* Very subtle background for definition */
            border: 1px solid #eee; /* Light border */
            border-radius: 5px; /* Rounded corners for a softer look */
        }
        .order-meta-info h5 {
            margin: 0 0 8px 0; /* Adjusted margin for h5 */
            font-size: 1.1em;
            display: block; /* Ensure it's a block element */
            text-align: center; /* Center the order ID line */
        }
        .order-meta-info span {
            display: inline-block; /* Keep labels and values inline */
            margin-left: 5px;
        }
        .order-meta-info .label {
            font-weight: bold;
            color: #555;
        }
        .order-meta-info .value {
            color: #333;
        }
        .order-meta-info div {
            margin-bottom: 5px; /* Space between meta info lines */
        }

        .info-block {
            margin-bottom: 20px; /* Increased margin for better separation */
            padding-bottom: 15px; /* More padding below content */
            border-bottom: 1px dashed #ccc; /* Slightly darker dashed line */
        }
        .info-block h5 {
            font-size: 1.15em; /* Slightly larger header */
            color: #007bff; /* Highlight info headers */
            margin-bottom: 10px; /* More space below header */
            border-bottom: 1px solid #bbb; /* Clearer border below header */
            padding-bottom: 6px;
        }
        .info-block div {
            margin-bottom: 6px; /* More space between detail lines */
        }
        .info-block .detail-label {
            font-weight: bold;
            width: 90px; /* Increased width for better alignment of labels */
            display: inline-block;
            vertical-align: top;
            color: #555;
        }
        .info-block .detail-value {
            display: inline-block;
            width: calc(100% - 100px); /* Adjust width based on label width and padding */
            vertical-align: top;
            color: #333;
        }
        /* Specific adjustment for address to ensure it wraps correctly without colon on new line */
        .info-block .text-break .detail-value {
            display: inline-block; /* Keep it inline-block */
            width: calc(100% - 90px); /* Ensure it takes available space */
        }


        /* Table styles */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px; /* More space above table */
            font-size: 0.98em; /* Slightly larger table font */
        }
        .invoice-table th, .invoice-table td {
            padding: 12px 10px; /* Increased padding within cells */
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .invoice-table th {
            background-color: #e9ecef; /* Slightly darker header background */
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
        }
        .invoice-table tr:last-child td {
            border-bottom: none; /* No border on last row */
        }
        .item-name {
            font-weight: bold;
            color: #333;
        }
        .item-variation {
            font-size: 0.88em; /* Slightly larger variation font */
            color: #666;
            margin-top: 5px; /* More space above variations */
            line-height: 1.3;
        }
        .invoice-table .text-center {
            text-align: center;
        }
        .invoice-table .text-right {
            text-align: right;
        }

        /* Checkout summary table */
        .checkout-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px; /* More space above summary */
            padding-top: 20px; /* More padding above summary content */
            border-top: 2px solid #bbb; /* Clearer, slightly darker border for summary */
        }
        .checkout-summary-table td {
            padding: 6px 0; /* More vertical padding for summary lines */
            line-height: 1.4;
        }
        .checkout-summary-table .summary-label {
            text-align: right;
            font-weight: normal;
            padding-right: 15px; /* Increased space between label and value */
            color: #555;
            width: 50%; /* Distribute width evenly */
        }
        .checkout-summary-table .summary-value {
            text-align: right;
            font-weight: bold;
            padding-left: 15px; /* Increased space between label and value */
            color: #333;
            width: 50%; /* Distribute width evenly */
        }
        .checkout-summary-table .total-row td {
            font-weight: bold;
            font-size: 1.3em; /* More emphasis on total */
            color: #000;
            border-top: 1px solid #999; /* Stronger border for total line */
            padding-top: 12px; /* More padding above total line */
        }

        .payment-info {
            margin-top: 25px; /* More space above payment info */
            padding-top: 15px; /* More padding above content */
            border-top: 1px solid #eee;
            font-size: 0.95em; /* Slightly larger font */
            color: #555;
        }
        .payment-method-details {
            text-align: center; /* Center the items */
        }
        .payment-method-details span {
            display: inline-block; /* Treat each part as a block for spacing */
            margin: 0 15px; /* Horizontal spacing between components */
            white-space: nowrap; /* Prevent breaking in the middle of a detail */
        }

        /* Footer */
        .invoice-footer {
            margin-top: 50px; /* More space above footer */
            padding-top: 25px; /* More padding above footer content */
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 0.88em; /* Slightly larger footer font */
            color: #777;
        }
        .thank-you-banner {
            margin: 25px 0; /* Increased vertical margin */
            background-color: #f0f8ff; /* Consistent with receipt banner */
            padding: 12px 0;
            border-top: 1px solid #cceeff;
            border-bottom: 1px solid #cceeff;
            text-align: center;
        }
        .thank-you-text {
            font-size: 1.3em;
            font-weight: bold;
            color: #0056b3;
            letter-spacing: 1.5px;
        }

        /* Mpdf specific adjustments */
        /* Overriding flex-related styles for mPDF compatibility */
        .d-flex, .justify-content-center {
            display: block; /* Force block display where flex was used */
            text-align: center; /* Use text-align for centering */
        }
        .order-info-id h5 span {
            display: inline; /* Keep order ID parts inline */
        }
        .order-info-details .col-12 {
            padding-left: 0;
            padding-right: 0;
        }
        /* Ensure address lines break correctly without adding extra colons */
        .info-block .text-break .detail-value {
            display: inline-block; /* Keep it inline-block */
            width: calc(100% - 90px); /* Ensure it takes available space */
        }
    </style>
</head>
<body>
    <div class="content container">
        <div id="printableArea">
            <div class="invoice-header">
                @if ($order->store)
                    <img class="invoice-logo" src="{{ public_path('assets/admin/img/invoice-logo.png') }}" alt="Store Logo">
                    <h2 class="store-name">{{ $order->store->name }}</h2>
                    <div class="store-address">
                        {{ $order->store->address }}
                    </div>
                    <div class="store-phone">
                        <span class="label">{{ translate('messages.phone') }}</span> : <span>{{ $order->store->phone }}</span>
                    </div>
                @endif
            </div>

            <div class="receipt-banner">
                {{-- Decorative images are hidden via CSS for cleaner PDF --}}
                <div class="receipt-text">{{ translate('messages.cash_receipt') }}</div>
            </div>

            <div class="order-meta-info">
                <h5 class="order-id"><span class="label">{{ translate('order_id') }}</span> : <span class="value">{{ $order['id'] }}</span></h5>
                <div class="order-date">
                    <span class="label">{{ translate('messages.date') }}</span> : <span class="value">{{ date('d/M/Y ' . config('timeformat'), strtotime($order['created_at'])) }}</span>
                </div>
                @if ($order->store?->gst_status)
                    <div class="gst-info">
                        <span class="label">{{ translate('Gst No') }}</span> : <span class="value">{{ $order->store->gst_code }}</span>
                    </div>
                @endif
            </div>

            <div class="order-info-details">
                <div class="row">
                    @if ($order->order_type == 'parcel')
                        <div class="col-100 info-block">
                            @php($address = json_decode($order->delivery_address, true))
                            <h5>{{ translate('messages.sender_info') }}</h5>
                            <div>
                                <span class="detail-label">{{ translate('messages.sender_name') }}</span> <span class="detail-value">: {{ isset($address) ? $address['contact_person_name'] : ($order->customer ? $order->customer['f_name'] . ' ' . $order->customer['l_name'] : '') }}</span>
                            </div>
                            <div>
                                <span class="detail-label">{{ translate('messages.phone') }}</span> <span class="detail-value">: {{ isset($address) ? $address['contact_person_number'] : ($order->customer ? $order->customer['phone'] : '') }}</span>
                            </div>
                            <div class="text-break">
                                <span class="detail-label">{{ translate('messages.address') }}</span> <span class="detail-value">: {{ isset($address) ? $address['address'] : '' }}</span>
                            </div>

                            @php($address = $order->receiver_details)
                            <h5 class="mt-3">{{ translate('messages.receiver_info') }}</h5>
                            <div>
                                <span class="detail-label">{{ translate('messages.receiver_name') }}</span> <span class="detail-value">: {{ isset($address) ? $address['contact_person_name'] : '' }}</span>
                            </div>
                            <div>
                                <span class="detail-label">{{ translate('messages.phone') }}</span> <span class="detail-value">: {{ isset($address) ? $address['contact_person_number'] : '' }}</span>
                            </div>
                            <div class="text-break">
                                <span class="detail-label">{{ translate('messages.address') }}</span> <span class="detail-value">: {{ isset($address) ? $address['address'] : '' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="col-100 info-block">
                            @php($address = json_decode($order->delivery_address, true))
                            @if (!empty($address))
                                <h5>{{ translate('messages.customer_info') }}</h5>
                                <div>
                                    <span class="detail-label">{{ translate('messages.contact_name') }}</span> <span class="detail-value">: {{ isset($address['contact_person_name']) ? $address['contact_person_name'] : '' }}</span>
                                </div>
                                <div>
                                    <span class="detail-label">{{ translate('messages.phone') }}</span> <span class="detail-value">: {{ isset($address['contact_person_number']) ? $address['contact_person_number'] : '' }}</span>
                                </div>
                                <div class="text-break">
                                    <span class="detail-label">{{ translate('messages.address') }}</span> <span class="detail-value">: {{ isset($address['address']) ? $address['address'] : '' }}</span>
                                </div>
                            @elseif ($order->customer)
                                <h5>{{ translate('messages.customer_info') }}</h5>
                                <div>
                                    <span class="detail-label">{{ translate('messages.contact_name') }}</span> <span class="detail-value">: {{ $order->customer?->f_name .' '.$order->customer?->l_name }}</span>
                                </div>
                                <div>
                                    <span class="detail-label">{{ translate('messages.phone') }}</span> <span class="detail-value">: {{ $order->customer?->phone}}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.description') }}</th>
                            <th class="text-center" style="width: 15%;">{{ translate('messages.qty') }}</th>
                            <th class="text-right" style="width: 25%;">{{ translate('messages.price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($order->order_type == 'parcel')
                            <tr>
                                <td>{{ translate('messages.delivery_charge') }}</td>
                                <td class="text-center">1</td>
                                <td class="text-right">{{ \App\CentralLogics\Helpers::format_currency($order->delivery_charge) }}</td>
                            </tr>
                        @else
                            @php($sub_total = 0)
                            <?php
                            if ($order->prescription_order == 1) {
                                $sub_total = $order['order_amount'] - $order['delivery_charge'] - $order['total_tax_amount'] - $order['dm_tips'] + $order['store_discount_amount'];
                            }
                            ?>
                            @php($total_tax = 0)
                            @php($total_dis_on_pro = 0)
                            @php($add_ons_cost = 0)
                            @foreach ($order->details as $detail)
                                @php($item = json_decode($detail->item_details, true))
                                <tr>
                                    <td>
                                        <span class="item-name">{{ $item['name'] }}</span> <br>
                                        @if ($order->store && $order->store->module->module_type == 'food')
                                            @if (count(json_decode($detail['variation'], true)) > 0)
                                                <div class="item-variation">
                                                    <strong><u>{{ translate('messages.variation') }} : </u></strong>
                                                    @foreach (json_decode($detail['variation'], true) as $variation)
                                                        @if (isset($variation['name']) && isset($variation['values']))
                                                            <span class="d-block text-capitalize">
                                                                <strong>{{ $variation['name'] }} - </strong>
                                                            </span>
                                                            @foreach ($variation['values'] as $value)
                                                                <span class="d-block text-capitalize">
                                                                    &nbsp; &nbsp; {{ $value['label'] }} :
                                                                    <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            @if (isset(json_decode($detail['variation'], true)[0]))
                                                                @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                    <div class="font-size-sm text-body">
                                                                        <span>{{ $key1 }} : </span>
                                                                        <span class="font-weight-bold">{{ $variation }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @break
                                                    @endif
                                                @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $detail['quantity'] }}
                                    </td>
                                    <td class="text-right">
                                        @php($amount = $detail['price'] * $detail['quantity'])
                                        {{ \App\CentralLogics\Helpers::format_currency($amount) }}
                                    </td>
                                </tr>
                                
                                @php($sub_total += $amount)
                                @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
                            @endforeach
                            
                        @endif
                    </tbody>
                </table>

                <table class="checkout-summary-table">
                    <tbody>
                        {{-- Sub Total --}}
                        <tr>
                            <td class="summary-label">{{ translate('messages.item_price') }}:</td>
                            <td class="summary-value">{{ \App\CentralLogics\Helpers::format_currency($sub_total) }}</td>
                        </tr>
                     

                        

                        {{-- Total --}}
                        <tr class="total-row">
                            <td class="summary-label">{{ translate('messages.total') }}:</td>
                            <td class="summary-value">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</td>
                        </tr>

                        {{-- Payments --}}
                        @if ($order?->payments)
                            @foreach ($order?->payments as $payment)
                                <tr>
                                    <td class="summary-label">
                                        @if ($payment->payment_status == 'paid')
                                            @if ($payment->payment_method == 'cash_on_delivery')
                                                {{ translate('messages.Paid_with_Cash') }} ({{ translate('COD') }}):
                                            @else
                                                {{ translate('messages.Paid_by') }} {{ translate($payment->payment_method) }}:
                                            @endif
                                        @else
                                            {{ translate('messages.Due_Amount') }} ({{ $payment->payment_method == 'cash_on_delivery' ? translate('messages.COD') : translate($payment->payment_method) }}):
                                        @endif
                                    </td>
                                    <td class="summary-value">
                                        {{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                
            </div>

            <div class="invoice-footer">
                <div class="thank-you-banner">
                    {{-- Decorative images are hidden via CSS for cleaner PDF --}}
                    <div class="thank-you-text">{{ translate('THANK YOU') }}</div>
                </div>
                <div class="copyright">
                    &copy; {{ \App\Models\BusinessSetting::where(['key' => 'business_name'])->first()->value }}.
                    <span>{{ \App\Models\BusinessSetting::where(['key' => 'footer_text'])->first()->value }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
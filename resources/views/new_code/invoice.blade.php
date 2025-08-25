<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 30px;
            font-size: 10.5px;
            line-height: 1.5;
            color: #333;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }

        /* Container and layout */
        .container {
            width: 100%;
            margin: 0 auto;
            max-width: 400px; /* Constrain width to resemble a receipt */
        }

        /* Header section */
        .invoice-header {
            margin-bottom: 20px;
            text-align: center;
        }
        .invoice-logo-box {
            border: 1px solid #000;
            padding: 15px 0;
            margin-bottom: 10px;
        }
        .store-name {
            font-size: 1.2em; /* Smaller font for a receipt-like feel */
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header-info-line {
            font-size: 0.9em;
            margin-bottom: 3px;
        }

        /* Invoice details */
        .invoice-details {
            margin-bottom: 20px;
        }

        /* Table styles */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.98em;
        }
        .invoice-table th, .invoice-table td {
            padding: 8px 5px;
            text-align: left;
        }
        .invoice-table th {
            font-weight: bold;
            text-transform: capitalize; /* Match the image style */
            border-bottom: 1px dashed #000; /* Dashed line for a receipt look */
            border-top: 1px dashed #000;
        }
        .invoice-table td {
            border-bottom: none;
        }
        .invoice-table .text-center { text-align: center; }
        .invoice-table .text-right { text-align: right; }

        /* Checkout summary table */
        .checkout-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .checkout-summary-table td {
            padding: 4px 0;
            line-height: 1.4;
        }
        .checkout-summary-table .summary-label {
            text-align: left; /* Aligned to left as per image */
            font-weight: normal;
        }
        .checkout-summary-table .summary-value {
            text-align: right;
            font-weight: bold;
        }
        .checkout-summary-table .subtotal-row,
        .checkout-summary-table .vat-row {
            border-bottom: 1px dashed #000;
        }
        .checkout-summary-table .total-row td {
            font-weight: bold;
            font-size: 1.1em;
            padding-top: 10px;
        }

        /* Separator lines */
        .line-separator {
            border-bottom: 1px dashed #000;
            margin: 20px 0;
        }
        .double-line-separator {
            border-top: 3px double #000;
            margin-top: 10px;
        }

        /* QR Code section */
        .qr-code-section {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="content container">
        <div id="printableArea">
            <div class="invoice-header text-center">
                <div style="margin-bottom:20px;">
                <img src="{{url('/storage/app/public/store/')}}/{{$order->store->logo}}" style=" max-width:50px">
                </div>
                <div class="store-name">{{ $order->store->name }} </div>
                @if($vendor->vat)
                <div class="header-info-line">VAT No: {{$vendor->vat}} </div>
                @endif
                <div class="header-info-line">Invoice No: {{ $order->id}}</div>
                <div class="header-info-line">Date: {{Carbon\Carbon::parse($order->created_at)->format('d/m/Y h:i:s A')}}</div>
            </div>

            <div class="invoice-details">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php($total_tax = 0)
                            @php($total_dis_on_pro = 0)
                            @php($add_ons_cost = 0)
                            @php($sub_total = 0)
                            <?php
                            if ($order->prescription_order == 1) {
                                $sub_total = $order['order_amount'] - $order['delivery_charge'] - $order['total_tax_amount'] - $order['dm_tips'] + $order['store_discount_amount'];
                            }
                            ?>
                            @foreach ($order->details as $detail)
                            <tr>
                                @php($item = json_decode($detail->item_details, true))
                            <td>{{ $item['name'] }}</td>
                            <td class="text-center">{{ $detail['quantity'] }}</td>
                            <td class="text-right">@php($amount = $item['price'] * $detail['quantity'])
                                       {{ \App\CentralLogics\Helpers::format_currency($amount) }}</td>
                            <td class="text-right">@php($amount = $item['price'] * $detail['quantity'])
                                        {{ \App\CentralLogics\Helpers::format_currency($amount) }}</td>
                            </tr>
                            @php($sub_total += $amount)
                                @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
                            @endforeach
                        
                    </tbody>
                </table>

                <div class="line-separator"></div>

                <table class="checkout-summary-table">
                    <tbody>
                        <tr>
                            <td class="summary-label">Subtotal</td>
                            <td class="text-right summary-value"> {{ \App\CentralLogics\Helpers::format_currency($amount) }}</td>
                        </tr>
                        <tr>
                            @if($vendor->vat)
                            <td class="summary-label">VAT %15</td>
                            <td class="text-right summary-value">{{ \App\CentralLogics\Helpers::format_currency($order->vatamt) }}</td>
                            @else
                            <td class="summary-label">VAT %0</td>
                            <td class="text-right summary-value">{{ \App\CentralLogics\Helpers::format_currency(0) }}</td>
                            @endif
                            
                        </tr>
                        <tr class="total-row">
                            <td class="summary-label">Total with VAT</td>
                            <td class="text-right summary-value">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="double-line-separator"></div>
            <div class="qr-code-section">
                <!-- <div style="width: 100px; height: 100px; border: 1px solid #000; margin: 0 auto;"></div> -->
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">Payment & Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for fonts and animations */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap'); /* Arabic font */

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }

        /* Subtle fade-in animation for sections */
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Delay for sequential fade-in */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Toggle switch styling */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #8B5CF6; /* Purple-500 */
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #8B5CF6;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* RTL specific styles */
        body.rtl {
            direction: rtl;
            font-family: 'Noto Sans Arabic', sans-serif;
        }

        body.rtl .text-right-on-rtl {
            text-align: right;
        }

        body.rtl .text-left-on-rtl {
            text-align: left;
        }

        body.rtl .sm\:text-left-on-rtl {
            text-align: right;
        }

        body.rtl .sm\:text-right-on-rtl {
            text-align: left;
        }

        /* Adjust toggle slider for RTL */
        body.rtl .toggle-switch .slider:before {
            left: auto;
            right: 4px;
            transform: translateX(0); /* Default position for RTL */
        }

        body.rtl .toggle-switch input:checked + .slider:before {
            transform: translateX(-26px); /* Move left in RTL */
        }

        body.rtl .flex-row-reverse-on-rtl {
            flex-direction: row-reverse;
        }

        /* Ensure card input details hide smoothly */
        .card-details-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            opacity: 0;
        }
        .card-details-section.open {
            max-height: 500px; /* Adjust based on content height */
            padding-top: 1.5rem; /* pt-6 */
            padding-bottom: 1.5rem; /* pb-6 */
            opacity: 1;
        }

        /* Custom style to force placeholder to the right in RTL */
        body.rtl input::placeholder {
            text-align: right;
        }
        body.rtl input {
            text-align: right; /* Also align the typed text to the right */
        }
    </style>
</head>
<?php $currentLang = "ar";?>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex flex-col items-center justify-center @if($currentLang === 'ar') rtl @endif">

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto">

        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0" style="display:none">
            <button id="lang-en" class="px-4 py-2 @if($currentLang === 'en') bg-purple-500 text-white @else bg-gray-200 text-gray-700 @endif rounded-lg shadow-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200">
                EN
            </button>
            <button id="lang-ar" class="px-4 py-2 @if($currentLang === 'ar') bg-purple-500 text-white @else bg-gray-200 text-gray-700 @endif rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200">
                AR
            </button>
        </div>

        <div class="p-6 sm:p-8 bg-gradient-to-r from-purple-600 to-indigo-700 text-white text-center rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="checkoutHeader">
                @if($currentLang === 'ar') الدفع @else Checkout @endif
            </h1>
            <p class="text-indigo-200 text-lg" data-key="reviewOrder">
                @if($currentLang === 'ar') راجع طلبك واختر طريقة الدفع. @else Review your order and choose payment method. @endif
            </p>
        </div>

        <div class="p-6 sm:p-8 fade-in delay-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-purple-200 pb-2" data-key="yourOrder">
                @if($currentLang === 'ar') طلبك @else Your Order @endif
            </h2>
            <ul id="order-summary-list" class="space-y-3 mb-6">
                {{-- Header Row for Order Summary --}}
                <li class="flex justify-between items-center font-semibold text-gray-700 border-b border-gray-300 pb-2">
                    <span class="w-1/2 @if($currentLang === 'ar') text-right @else text-left @endif">
                        @if($currentLang === 'ar') المنتج @else Product @endif
                    </span>
                    <span class="w-1/6 text-center">
                        @if($currentLang === 'ar') الكمية @else Qty @endif
                    </span>
                    <span class="w-1/6 text-center">
                        @if($currentLang === 'ar') السعر @else Price @endif
                    </span>
                    <span class="w-1/6 @if($currentLang === 'ar') text-left @else text-right @endif">
                        @if($currentLang === 'ar') الإجمالي @else Total @endif
                    </span>
                </li>

                @forelse($checkoutItems as $item)
                    <li class="flex justify-between items-center bg-white p-3 rounded-md shadow-sm">
                        <span class="text-gray-700 font-medium w-1/2 @if($currentLang === 'ar') text-right @else text-left @endif">{{ $item['name'] }}</span>
                        <span class="text-gray-600 w-1/6 text-center">{{ $item['quantity'] }}</span>
                        <span class="text-gray-600 w-1/6 text-center">{{ number_format($item['price_per_kg'], 2) }}</span>
                        <span class="font-semibold text-purple-700 w-1/6 @if($currentLang === 'ar') text-left @else text-right @endif">{{ number_format($item['total_item_price'], 2) }}</span>
                    </li>
                @empty
                    <li class="text-gray-600 text-center py-4">
                        @if($currentLang === 'ar') لم يتم اختيار أي عناصر. يرجى العودة إلى كتالوج المنتجات. @else No items selected. Please go back to the product catalog. @endif
                    </li>
                @endforelse
            </ul>

            <div class="flex justify-between items-center bg-purple-50 p-4 rounded-lg shadow-inner">
                <span class="text-xl font-bold text-gray-800" data-key="orderTotal">
                    @if($currentLang === 'ar') إجمالي الطلب: @else Order Total: @endif
                </span>
                <span id="final-total-amount" class="text-2xl font-extrabold text-purple-800">
                    {{ number_format($overallTotal, 2) }} @if($currentLang === 'ar') ريال @else yuan @endif
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 border-t border-gray-200 fade-in delay-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-purple-200 pb-2" data-key="customerInformation">
                @if($currentLang === 'ar') معلومات العميل @else Customer Information @endif
            </h2>
            <div class="space-y-4">
                <div>
                    <label for="customer-name" class="block text-sm font-medium text-gray-700 mb-1" data-key="fullName">
                        @if($currentLang === 'ar') الاسم الكامل @else Full Name @endif
                    </label>
                    <input type="text" id="customer-name" placeholder="@if($currentLang === 'ar') الاسم الكامل @else Full Name @endif" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="customer-email" class="block text-sm font-medium text-gray-700 mb-1" data-key="emailAddress">
                        @if($currentLang === 'ar') عنوان البريد الإلكتروني @else Email Address @endif
                    </label>
                    <input type="email" id="customer-email" placeholder="@if($currentLang === 'ar') البريد الإلكتروني @else email@example.com @endif" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="customer-phone" class="block text-sm font-medium text-gray-700 mb-1" data-key="phoneNumber">
                        @if($currentLang === 'ar') رقم الهاتف @else Phone Number @endif
                    </label>
                    <input type="tel" id="customer-phone" placeholder="@if($currentLang === 'ar') رقم الهاتف @else +1 234 567 8900 @endif" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 border-t border-gray-200 fade-in delay-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-purple-200 pb-2" data-key="paymentOptions">
                @if($currentLang === 'ar') خيارات الدفع @else Payment Options @endif
            </h2>

            <div class="space-y-4">
                <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow-sm">
                    <input type="radio" id="payment-applepay" name="payment-method" value="applepay" class="form-radio text-purple-600 h-5 w-5" checked>
                    <label for="payment-applepay" class="ml-3 text-lg font-medium text-gray-700 flex items-center flex-row-reverse-on-rtl">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Apple_Pay_logo.svg/2560px-Apple_Pay_logo.svg.png" alt="Apple Pay" class="h-6 ml-2 rtl:mr-2 rtl:ml-0">
                        
                    </label>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow-sm" style="display:none">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="radio" id="payment-card" name="payment-method" value="card" class="form-radio text-purple-600 h-5 w-5">
                            <label for="payment-card" class="ml-3 text-lg font-medium text-gray-700" data-key="creditDebitCard">
                                @if($currentLang === 'ar') بطاقة ائتمان/خصم @else Credit/Debit Card @endif
                            </label>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="card-toggle">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div id="card-details" class="card-details-section mt-4 pt-6 pb-6 border-t border-gray-200 hidden">
                        <div class="space-y-4">
                            <div>
                                <label for="card-number" class="block text-sm font-medium text-gray-700 mb-1" data-key="cardNumber">
                                    @if($currentLang === 'ar') رقم البطاقة @else Card Number @endif
                                </label>
                                <input type="text" id="card-number" placeholder="XXXX XXXX XXXX XXXX" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div class="flex space-x-4 flex-row-reverse-on-rtl">
                                <div class="flex-1">
                                    <label for="expiry-date" class="block text-sm font-medium text-gray-700 mb-1" data-key="expiryDate">
                                        @if($currentLang === 'ar') تاريخ انتهاء الصلاحية @else Expiry Date @endif
                                    </label>
                                    <input type="text" id="expiry-date" placeholder="MM/YY" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                </div>
                                <div class="flex-1">
                                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1" data-key="cvv">
                                        @if($currentLang === 'ar') CVV @else CVV @endif
                                    </label>
                                    <input type="text" id="cvv" placeholder="123" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                </div>
                            </div>
                            <div>
                                <label for="card-name" class="block text-sm font-medium text-gray-700 mb-1" data-key="nameOnCard">
                                    @if($currentLang === 'ar') الاسم على البطاقة @else Name on Card @endif
                                </label>
                                <input type="text" id="card-name" placeholder="Full Name" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-gray-100 rounded-b-xl border-t border-gray-200 text-center fade-in delay-300">
            <button id="pay-now-button" class="w-full px-8 py-4 bg-green-500 text-white font-bold rounded-lg shadow-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75 transition duration-300 ease-in-out transform hover:scale-105">
                <span data-key="payNow">
                    @if($currentLang === 'ar') ادفع الآن @else Pay Now @endif
                </span>
            </button>
        </div>

    </div>

    <script>
        // Data passed from Laravel controller (accessing Blade variables)
        const checkoutItems = @json($checkoutItems);
        const overallTotal = @json($overallTotal);
        let currentLang = @json($currentLang);

        // Translations object
        const translations = {
            en: {
                pageTitle: "Payment & Checkout",
                checkoutHeader: "Checkout",
                reviewOrder: "Review your order and choose payment method.",
                yourOrder: "Your Order",
                customerInformation: "Customer Information",
                fullName: "Full Name",
                emailAddress: "Email Address",
                phoneNumber: "Phone Number",
                orderTotal: "Order Total:",
                paymentOptions: "Payment Options",
                applePay: "Apple Pay",
                creditDebitCard: "Credit/Debit Card",
                cardNumber: "Card Number",
                expiryDate: "Expiry Date",
                cvv: "CVV",
                nameOnCard: "Name on Card",
                payNow: "Pay Now",
                quantity: "Qty",
                price: "Price",
                total: "Total",
                currency: "yuan",
                currencySAR: "SAR",
                noItemsSelected: "No items selected. Please go back to the product catalog.",
                enterCardDetails: "Please enter all card details.",
                enterCustomerDetails: "Please enter your full name, email, and phone number.",
                productNameHeader: "Product",
                paymentFailed: "Payment failed: ",
                unknownError: "Unknown error",
                networkError: "An error occurred during payment. Please try again.",
                orderConfirmationFailed: "Failed to confirm order details: ",
                confirmingOrder: "Confirming order details..."
            },
            ar: {
                pageTitle: "الدفع وإتمام الطلب",
                checkoutHeader: "الدفع",
                reviewOrder: "راجع طلبك واختر طريقة الدفع.",
                yourOrder: "طلبك",
                customerInformation: "معلومات العميل",
                fullName: "الاسم الكامل",
                emailAddress: "عنوان البريد الإلكتروني",
                phoneNumber: "رقم الهاتف",
                orderTotal: "إجمالي الطلب:",
                paymentOptions: "خيارات الدفع",
                applePay: "أبل باي",
                creditDebitCard: "بطاقة ائتمان/خصم",
                cardNumber: "رقم البطاقة",
                expiryDate: "تاريخ انتهاء الصلاحية",
                cvv: "CVV",
                nameOnCard: "الاسم على البطاقة",
                payNow: "ادفع الآن",
                quantity: "الكمية",
                price: "السعر",
                total: "الإجمالي",
                currency: "ريال",
                currencySAR: "ريال",
                noItemsSelected: "لم يتم اختيار أي عناصر. يرجى العودة إلى كتالوج المنتجات.",
                enterCardDetails: "الرجاء إدخال جميع تفاصيل البطاقة.",
                enterCustomerDetails: "الرجاء إدخال اسمك الكامل وبريدك الإلكتروني ورقم هاتفك.",
                productNameHeader: "المنتج",
                paymentFailed: "فشل الدفع: ",
                unknownError: "خطأ غير معروف",
                networkError: "حدث خطأ أثناء الدفع. الرجاء المحاولة مرة أخرى.",
                orderConfirmationFailed: "فشل تأكيد تفاصيل الطلب: ",
                confirmingOrder: "جاري تأكيد تفاصيل الطلب..."
            }
        };

        // Function to update content based on language
        function updateContent(lang) {
            currentLang = lang;
            const currentTranslations = translations[lang];

            for (const key in currentTranslations) {
                const element = document.querySelector(`[data-key="${key}"]`);
                if (element) {
                    element.textContent = currentTranslations[key];
                }
            }

            const body = document.body;
            const html = document.documentElement;

            if (lang === 'ar') {
                body.classList.add('rtl');
                html.setAttribute('dir', 'rtl');
            } else {
                body.classList.remove('rtl');
                html.removeAttribute('dir');
            }

            document.getElementById('lang-en').classList.remove('bg-purple-500', 'text-white');
            document.getElementById('lang-en').classList.add('bg-gray-200', 'text-gray-700');
            document.getElementById('lang-ar').classList.remove('bg-purple-500', 'text-white');
            document.getElementById('lang-ar').classList.add('bg-gray-200', 'text-gray-700');

            if (lang === 'en') {
                document.getElementById('lang-en').classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('lang-en').classList.add('bg-purple-500', 'text-white');
            } else if (lang === 'ar') {
                document.getElementById('lang-ar').classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('lang-ar').classList.add('bg-purple-500', 'text-white');
            }

            document.getElementById('final-total-amount').textContent = `${overallTotal.toFixed(2)} ${currentTranslations.currencySAR}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cardToggle = document.getElementById('card-toggle');
            const cardDetailsSection = document.getElementById('card-details');
            const paymentCardRadio = document.getElementById('payment-card');
            const paymentApplePayRadio = document.getElementById('payment-applepay');
            const payNowButton = document.getElementById('pay-now-button');

            if (currentLang === 'ar') {
                document.body.classList.add('rtl');
                document.documentElement.setAttribute('dir', 'rtl');
            } else {
                document.body.classList.remove('rtl');
                document.documentElement.removeAttribute('dir');
            }
            updateContent(currentLang);

            cardToggle.addEventListener('change', () => {
                if (cardToggle.checked) {
                    cardDetailsSection.classList.remove('hidden');
                    setTimeout(() => cardDetailsSection.classList.add('open'), 10);
                    paymentCardRadio.checked = true;
                } else {
                    cardDetailsSection.classList.remove('open');
                    cardDetailsSection.addEventListener('transitionend', function handler() {
                        cardDetailsSection.classList.add('hidden');
                        cardDetailsSection.removeEventListener('transitionend', handler);
                    });
                    paymentApplePayRadio.checked = true;
                }
            });

            paymentCardRadio.addEventListener('change', () => {
                if (paymentCardRadio.checked) {
                    cardToggle.checked = true;
                    cardDetailsSection.classList.remove('hidden');
                    setTimeout(() => cardDetailsSection.classList.add('open'), 10);
                }
            });

            paymentApplePayRadio.addEventListener('change', () => {
                if (paymentApplePayRadio.checked) {
                    cardToggle.checked = false;
                    cardDetailsSection.classList.remove('open');
                    cardDetailsSection.addEventListener('transitionend', function handler() {
                        cardDetailsSection.classList.add('hidden');
                        cardDetailsSection.removeEventListener('transitionend', handler);
                    });
                }
            });

            // "Pay Now" button functionality
            payNowButton.addEventListener('click', async () => {
                    function removeSpacesAndKeepLastNine(str) {
                        // 1. Remove all spaces from the string
                        const stringWithoutSpaces = str.replace(/\s/g, '');

                        // 2. Keep the last 9 characters
                        //    If the string is shorter than 9 characters, it will return the whole string.
                        const lastNineChars = stringWithoutSpaces.slice(-9);

                        return lastNineChars;
                    }
                const customerName = document.getElementById('customer-name').value;
                const customerEmail = document.getElementById('customer-email').value;
                const customerPhone = removeSpacesAndKeepLastNine(document.getElementById('customer-phone').value);
                    
                if (!customerName || !customerEmail || !customerPhone) {
                    
                    alert(translations[currentLang].enterCustomerDetails);
                    return;
                }
                console.log(checkoutItems);
                // Prepare the items data to send
                const itemsToSend = checkoutItems.map(item => ({
                    id: item.id,
                    price_per_kg: item.price_per_kg,
                    quantity: item.quantity
                }));

                const orderDetails = {
                    customer_name: customerName,
                    customer_email: customerEmail,
                    customer_phone: customerPhone,
                    total_amount: overallTotal,
                    items: itemsToSend // Include the prepared items array
                };

                payNowButton.disabled = true; // Disable button to prevent multiple clicks
                payNowButton.textContent = translations[currentLang].confirmingOrder; // Provide feedback

                try {
                    // Step 1: Send order details to your backend for confirmation/processing
                    const confirmOrderResponse = await fetch('/api/v1/add-to-cart-guest', { // New API endpoint
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                        },
                        body: JSON.stringify(orderDetails)
                    });

                    const confirmOrderData = await confirmOrderResponse.json();

                    if (!confirmOrderResponse.ok) {
                        alert(`${translations[currentLang].orderConfirmationFailed}${confirmOrderData.message || translations[currentLang].unknownError}`);
                        console.error('Order confirmation error:', confirmOrderData);
                        return;
                    }

                    // Assuming confirmation is successful, proceed with payment gateway redirection
                    // const payerFirstName = customerName.split(' ')[0] || '';
                    // const payerLastName = customerName.split(' ').slice(1).join(' ') || '';
                    const orderAmount = overallTotal.toFixed(2);
                    const orderCurrency = 'SAR';
                    const payerCountry = 'SA';
                    const payerCity = 'Riyadh'; // Example, could be from user input or default
                    const termUrl3ds = 'https://trymajlis.com/thank-you';
                    const random = confirmOrderData.order_id;// Math.floor(1000 + Math.random() * 9000); // Simple unique order ID for Edfapay

                   const edfapayUrl = `https://trymajlis.com/edfapay?order_id=${random}&order_amount=${orderAmount}&order_currency=SAR&payer_first_name=${customerName}&payer_last_name=${customerName}&payer_country=SA&payer_city=Riyad&payer_email=${customerEmail}&payer_phone=${customerPhone}&term_url_3ds=https://trymajlis.com/payment-processing/${random}`;
                    console.log(edfapayUrl);
                    // Step 2: Redirect to Edfapay gateway
                    const edfapayResponse = await fetch(edfapayUrl, {
                        method: 'GET', // Edfapay example uses GET for redirection
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    if (edfapayResponse.ok) {
                        const edfapayData = await edfapayResponse.json();
                        if (edfapayData.success && edfapayData.redirect_url) {
                            window.location.href = edfapayData.redirect_url; // Redirect to Edfapay
                        } else {
                            alert(`${translations[currentLang].paymentFailed}${edfapayData.message || translations[currentLang].unknownError}`);
                            console.error('Edfapay response error:', edfapayData);
                        }
                    } else {
                        const errorData = await edfapayResponse.json();
                        alert(`${translations[currentLang].paymentFailed}${errorData.message || translations[currentLang].unknownError}`);
                        console.error('Payment error:', errorData);
                    }
                } catch (error) {
                    console.error('Network error or unexpected:', error);
                    alert(translations[currentLang].networkError);
                } finally {
                    payNowButton.disabled = false; // Re-enable button
                    updateContent(currentLang); // Reset button text
                }
            });

            document.getElementById('lang-en').addEventListener('click', () => {
                updateContent('en');
            });
            document.getElementById('lang-ar').addEventListener('click', () => {
                updateContent('ar');
            });
        });
    </script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle"></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for fonts and animations */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap'); /* Arabic font */

        body {
            font-family: 'Inter', sans-serif;
            background-color: #DBDAD6; /* Light gray background from palette */
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

        /* Animation for total amount change */
        .total-highlight {
            animation: totalPulse 0.5s ease-out;
        }

        @keyframes totalPulse {
            0% { transform: scale(1); color: inherit; }
            50% { transform: scale(1.05); color: #EDAA4B; } /* Orange highlight from palette */
            100% { transform: scale(1); color: inherit; }
        }

        /* Hide default number input arrows */
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* RTL specific styles */
        body.rtl {
            direction: rtl;
            font-family: 'Noto Sans Arabic', sans-serif; /* Apply Arabic font */
        }

        body.rtl .text-right-on-rtl {
            text-align: right;
        }

        body.rtl .text-left-on-rtl {
            text-align: left;
        }

        /* Adjustments for buttons and quantities in RTL */
        /* For flex-row-reverse, space-x-2 acts as margin-right from the visual left */
        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-controls.rtl {
            flex-direction: row-reverse;
        }

        body.rtl .sm\:text-left-on-rtl {
            text-align: right;
        }

        body.rtl .sm\:text-right-on-rtl {
            text-align: left;
        }
    </style>
</head>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-[#DBDAD6] to-[#B0ACA7] min-h-screen flex flex-col items-center justify-center">

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto">

        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0" style="display:none">
            <button id="lang-en" class="px-4 py-2 bg-[#EDAA4B] text-white rounded-lg shadow-md hover:bg-[#C98A3C] focus:outline-none focus:ring-2 focus:ring-[#EDAA4B] focus:ring-opacity-75 transition duration-200">
                EN
            </button>
            <button id="lang-ar" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200">
                AR
            </button>
        </div>

        <div class="p-6 sm:p-8 bg-gradient-to-r from-[#EDAA4B] to-[#838484] text-white text-center rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="pageHeaderTitle"></h1>
            <p class="text-[#DBDAD6] text-lg" data-key="pageHeaderSubtitle"></p>
        </div>

        <div class="p-6 sm:p-8 flex-grow overflow-y-auto" style="max-height: calc(100vh - 250px);">
            <ul id="product-list" class="space-y-4">
                <li id="loading-message" class="text-gray-500 text-center py-4"></li>
            </ul>
        </div>

        <div class="p-6 sm:p-8 bg-gray-100 flex flex-col sm:flex-row items-center justify-between rounded-b-xl border-t border-gray-200 fade-in delay-500">
            <div class="text-2xl sm:text-3xl font-bold text-[#838484] mb-4 sm:mb-0">
                <span data-key="totalLabel"></span> <span id="total-amount" class="text-[#EDAA4B]"></span>
            </div>
            <button id="checkout-btn" class="w-full sm:w-auto px-8 py-3 bg-[#EDAA4B] text-white font-bold rounded-lg shadow-lg hover:bg-[#C98A3C] focus:outline-none focus:ring-2 focus:ring-[#EDAA4B] focus:ring-opacity-75 transition duration-300 ease-in-out transform hover:scale-105" data-key="checkoutButton">
            </button>
        </div>

    </div>

    <script>
        // Store product data fetched from API
        let productsData = [];

        // Default language
        let currentLang = 'ar';
        const storeId = @json($store_id);
        const currency = @json($currency);

        // Translations object
        const translations = {
            en: {
                pageTitle: "Product Catalog & Checkout",
                pageHeaderTitle: "Our Fresh Products",
                pageHeaderSubtitle: "Select your desired quantity and proceed to checkout.",
                unitName: "kg", // Changed from unitKg to unitName for broader use
                currencySymbol: "SAR", // New key for currency symbol
                totalLabel: "Total:",
                checkoutButton: "Proceed to Checkout",
                loadingProducts: "Loading products...",
                noProductsToDisplay: "No products to display.",
                errorLoadingProducts: "Failed to load products. Please try again later."
            },
            ar: {
                pageTitle: "كتالوج المنتجات والدفع",
                pageHeaderTitle: "منتجاتنا الطازجة",
                pageHeaderSubtitle: "اختر الكمية المطلوبة وتابع الدفع.",
                unitName: "كجم", // Changed from unitKg to unitName for broader use
                currencySymbol: currency, // New key for currency symbol
                totalLabel: "الإجمالي:",
                checkoutButton: "المتابعة إلى الدفع",
                loadingProducts: "جارٍ تحميل المنتجات...",
                noProductsToDisplay: "لا توجد منتجات لعرضها.",
                errorLoadingProducts: "فشل تحميل المنتجات. الرجاء المحاولة مرة أخرى لاحقًا."
            }
        };

        // Function to calculate and update the total
        function updateTotal() {
            let total = 0;
            const quantityInputs = document.querySelectorAll('input[type="number"][data-price]');
            const totalAmountSpan = document.getElementById('total-amount');

            quantityInputs.forEach(input => {
                const price = parseFloat(input.dataset.price);
                const quantity = parseFloat(input.value); // Use parseFloat for 0.5 step
                if (!isNaN(price) && !isNaN(quantity) && quantity >= 0) {
                    total += price * quantity;
                }
            });

            // Apply highlight animation
            totalAmountSpan.classList.remove('total-highlight');
            void totalAmountSpan.offsetWidth; // Trigger reflow to restart animation
            totalAmountSpan.classList.add('total-highlight');

            // Get current currency based on language
            const currency = translations[currentLang].currencySymbol;

            totalAmountSpan.textContent = total.toFixed(2) + " " + currency;
        }

        // Function to update content based on language
        function updateContent(lang) {
            currentLang = lang;
            const currentTranslations = translations[lang];

            // Set document title
            document.title = currentTranslations.pageTitle;

            // Update static text content using data-key
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
                // Apply rtl class to quantity control containers for flex-direction: row-reverse
                document.querySelectorAll('.quantity-controls').forEach(control => {
                    control.classList.add('rtl');
                });
                // Adjust text alignment for product details to right in RTL
                document.querySelectorAll('.product-details-grow').forEach(detail => {
                    detail.classList.remove('sm:text-left'); // Remove LTR-specific small screen left align
                    detail.classList.add('sm:text-right'); // Add RTL-specific small screen right align
                    detail.classList.remove('text-center'); // Remove default mobile center
                    detail.classList.add('text-right'); // Ensure mobile also aligns right
                });

            } else {
                body.classList.remove('rtl');
                html.removeAttribute('dir');
                // Remove rtl class from quantity control containers
                document.querySelectorAll('.quantity-controls').forEach(control => {
                    control.classList.remove('rtl');
                });
                 // Reset text alignment for product details to left in LTR
                document.querySelectorAll('.product-details-grow').forEach(detail => {
                    detail.classList.remove('sm:text-right'); // Remove RTL-specific small screen right align
                    detail.classList.add('sm:text-left'); // Add LTR-specific small screen left align
                    detail.classList.remove('text-right'); // Remove mobile right
                    detail.classList.add('text-center'); // Re-apply default mobile center
                });
            }

            // Update active language button styling
            document.getElementById('lang-en').classList.remove('bg-[#EDAA4B]', 'text-white');
            document.getElementById('lang-en').classList.add('bg-gray-200', 'text-gray-700');
            document.getElementById('lang-ar').classList.remove('bg-[#EDAA4B]', 'text-white');
            document.getElementById('lang-ar').classList.add('bg-gray-200', 'text-gray-700');

            if (lang === 'en') {
                document.getElementById('lang-en').classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('lang-en').classList.add('bg-[#EDAA4B]', 'text-white');
            } else if (lang === 'ar') {
                document.getElementById('lang-ar').classList.remove('bg-gray-200', 'text-gray-700');
                document.getElementById('lang-ar').classList.add('bg-[#EDAA4B]', 'text-white');
            }

            // Dynamically update product list
            const productListElement = document.getElementById('product-list');
            productListElement.innerHTML = ''; // Clear existing list (and loading message)

            if (productsData.length === 0) {
                const noProductsMessage = document.createElement('li');
                noProductsMessage.className = 'text-gray-600 text-center py-4';
                noProductsMessage.textContent = currentTranslations.noProductsToDisplay;
                productListElement.appendChild(noProductsMessage);
                return;
            }

            productsData.forEach(product => {
                const productIdSlug = product.name_en.toLowerCase().replace(/\s/g, '-').replace(/[^a-z0-9-]/g, '');
                const productName = lang === 'ar' ? product.name_ar : product.name_en;
                const productPrice = product.price.toFixed(2);
                // Combined price and unit for display
                const priceAndUnit = `${productPrice} ${currentTranslations.currencySymbol} / ${currentTranslations.unitName}`;

                const listItem = document.createElement('li');
                listItem.className = 'flex flex-col sm:flex-row justify-between items-center bg-[#DBDAD6] p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 fade-in';
                listItem.style.animationDelay = `${(productsData.indexOf(product) * 0.1) + 0.1}s`; // Stagger animation

                // The innerHTML structure for quantity controls has been reordered
                // to achieve the desired visual layout with flex-row and flex-row-reverse.
                // LTR (English):  [+] [Input] [-] [Unit]
                // RTL (Arabic):   [Unit] [-] [Input] [+] (visually from right to left)
                listItem.innerHTML = `
                    <div class="flex-grow mb-2 sm:mb-0 sm:mr-4 product-details-grow ${lang === 'ar' ? 'text-right sm:text-right' : 'text-center sm:text-left'}">
                        <span class="text-lg font-medium text-[#838484]">${productName}</span>
                        <p class="text-sm text-[#B0ACA7]">${priceAndUnit}</p>
                    </div>
                    <div class="flex items-center space-x-2 quantity-controls ${lang === 'ar' ? 'rtl' : ''}">
                        <button class="quantity-btn p-2 bg-[#EDAA4B] text-white rounded-md hover:bg-[#C98A3C] focus:outline-none focus:ring-2 focus:ring-[#EDAA4B] focus:ring-opacity-75 transition duration-200 w-8 h-8 flex items-center justify-center text-xl font-bold" data-action="decrement" data-target="qty-${productIdSlug}">-</button>
                        <label for="qty-${productIdSlug}" class="sr-only">Quantity for ${productName}</label>
                        <input type="number" id="qty-${productIdSlug}" data-product-id="${productIdSlug}" data-price="${product.price}" value="0.0" min="0" step="1" class="w-20 p-2 border border-gray-300 rounded-md text-center focus:ring-2 focus:ring-[#EDAA4B] focus:border-transparent transition-all duration-200">
                        <button class="quantity-btn p-2 bg-[#EDAA4B] text-white rounded-md hover:bg-[#C98A3C] focus:outline-none focus:ring-2 focus:ring-[#EDAA4B] focus:ring-opacity-75 transition duration-200 w-8 h-8 flex items-center justify-center text-xl font-bold" data-action="increment" data-target="qty-${productIdSlug}">+</button>
                        <span class="text-[#838484] font-medium unit-display">${currentTranslations.unitName}</span>
                    </div>
                `;
                productListElement.appendChild(listItem);
            });

            // Re-attach event listeners after dynamic content is added
            attachQuantityEventListeners();
            updateTotal(); // Ensure total is updated after rendering
        }

        // Attaches event listeners to dynamically created quantity controls
        function attachQuantityEventListeners() {
            const quantityInputs = document.querySelectorAll('input[type="number"][data-price]');
            const quantityButtons = document.querySelectorAll('.quantity-btn');

            quantityButtons.forEach(button => {
                button.removeEventListener('click', handleQuantityButtonClick); // Prevent double-listening
                button.addEventListener('click', handleQuantityButtonClick);
            });

            quantityInputs.forEach(input => {
                input.removeEventListener('input', updateTotal);
                input.removeEventListener('change', updateTotal);
                input.addEventListener('input', updateTotal);
                input.addEventListener('change', updateTotal);
            });
        }

        function handleQuantityButtonClick(event) {
            const action = event.target.dataset.action;
            const targetId = event.target.dataset.target;
            const input = document.getElementById(targetId);
            let currentValue = parseFloat(input.value); // Use parseFloat for 0.5 step

            if (action === 'increment') {
                input.value = (currentValue + 1).toFixed(1); // Increment by 1, format to 1 decimal
            } else if (action === 'decrement') {
                if (currentValue > 0) {
                    input.value = (currentValue - 1).toFixed(1); // Decrement by 1, format to 1 decimal
                    if (parseFloat(input.value) < 0) { // Ensure it doesn't go below 0
                        input.value = "0.0";
                    }
                }
            }
            updateTotal();
        }

        // Function to fetch products from the API
        async function fetchProducts() {
            const productListElement = document.getElementById('product-list');
            productListElement.innerHTML = `<li id="loading-message" class="text-gray-500 text-center py-4">${translations[currentLang].loadingProducts}</li>`;

            try {
                const response = await fetch(`https://waslqr.com/api/v1/items/get-products-demo?store_id=${storeId}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const apiResponse = await response.json();

                // Map the API response structure to the desired productsData format
                productsData = apiResponse.map(item => ({
                    id: item.id,
                    name_en: item.translations.find(t => t.locale === 'en' && t.key === 'name')?.value || item.name,
                    name_ar: item.translations.find(t => t.locale === 'ar' && t.key === 'name')?.value || item.name,
                    price: item.price // Use 'price' directly from the API response
                }));

                // Once data is fetched and mapped, update the content for the current language
                updateContent(currentLang);

            } catch (error) {
                console.error("Error fetching products:", error);
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.textContent = translations[currentLang].errorLoadingProducts;
                    loadingMessage.style.color = 'red'; // Indicate error
                }
            }
        }

        // Checkout Button Logic for Laravel Backend
        document.getElementById('checkout-btn').addEventListener('click', async () => {
            const selectedItems = [];
            document.querySelectorAll('input[type="number"][data-price]').forEach(input => {
                const quantity = parseFloat(input.value); // Use parseFloat here too
                if (quantity > 0) {
                    const productId = input.dataset.productId;
                    const price = parseFloat(input.dataset.price);

                    // Find the product name by going up to the list item and finding the span
                    const productNameElement = input.closest('li').querySelector('.product-details-grow span');
                    const productName = productNameElement ? productNameElement.textContent : productId;

                    selectedItems.push({
                        id: productId,
                        name: productName,
                        quantity: quantity,
                        price_per_kg: price // Keep as price_per_kg for Laravel backend
                    });
                }
            });

            if (selectedItems.length === 0) {
                alert("Please select at least one item to proceed to checkout.");
                return;
            }

            // In a real Laravel app, you would send this to a backend endpoint like /checkout/process
            // For this self-contained HTML, we'll just log and alert
            console.log("Items to checkout:", selectedItems);
           // alert("Proceeding to checkout with selected items (Check console for details). In a real app, this would redirect.");

            // Example of a hypothetical fetch call to a Laravel backend:

            const currentLang = document.documentElement.getAttribute('dir') === 'rtl' ? 'ar' : 'en';
            try {
                const response = await fetch('/checkout/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                       'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token for security
                    },
                    body: JSON.stringify({
                        items: selectedItems,
                        lang: currentLang
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert("Something went wrong with the redirect.");
                    }
                } else {
                    const errorData = await response.json();
                    alert(`Checkout failed: ${errorData.message || 'Unknown error'}`);
                    console.error('Checkout error:', errorData);
                }
            } catch (error) {
                console.error('Network error or unexpected:', error);
                alert("An error occurred. Please try again.");
            }

        });


        // Event listeners for language buttons
        document.getElementById('lang-en').addEventListener('click', () => updateContent('en'));
        document.getElementById('lang-ar').addEventListener('click', () => updateContent('ar'));

        // Initial content load and fetch products
        document.addEventListener('DOMContentLoaded', () => {
            updateContent(currentLang); // Update static content initially
            fetchProducts(); // Start fetching products
        });
    </script>

</body>
</html>
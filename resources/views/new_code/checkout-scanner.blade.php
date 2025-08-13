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

        /* Animation for total amount change */
        .total-highlight {
            animation: totalPulse 0.5s ease-out;
        }

        @keyframes totalPulse {
            0% { transform: scale(1); color: inherit; }
            50% { transform: scale(1.05); color: #6D28D9; } /* Purple highlight */
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
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex flex-col items-center justify-center">

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto">

        <!-- Language selection buttons - Hidden by default as per request -->
        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0 hidden">
            <button id="lang-en" class="px-4 py-2 bg-purple-500 text-white rounded-lg shadow-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200">
                EN
            </button>
            <button id="lang-ar" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200">
                AR
            </button>
        </div>

        <div class="p-6 sm:p-8 bg-gradient-to-r from-purple-600 to-indigo-700 text-white text-center rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="pageHeaderTitle"></h1>
            <p class="text-indigo-200 text-lg" data-key="pageHeaderSubtitle"></p>
        </div>

        <div class="p-6 sm:p-8 flex-grow overflow-y-auto" style="max-height: calc(100vh - 250px);">
            <ul id="product-list" class="space-y-4">
                {{-- Products will be rendered here by JavaScript --}}
                <li id="loading-message" class="text-gray-500 text-center py-4"></li>
            </ul>
        </div>

        <div class="p-6 sm:p-8 bg-gray-100 flex flex-col sm:flex-row items-center justify-between rounded-b-xl border-t border-gray-200 fade-in delay-500">
            <div class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-0">
                <span data-key="totalLabel"></span> <span id="total-amount" class="text-purple-700"></span>
            </div>
            <button id="checkout-btn" class="w-full sm:w-auto px-8 py-3 bg-purple-600 text-white font-bold rounded-lg shadow-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-300 ease-in-out transform hover:scale-105" data-key="checkoutButton">
            </button>
        </div>

    </div>

    <script>
        // Store product data fetched from Laravel Blade
        let productsData = @json($checkoutItems);
        let initialOverallTotal = @json($overallTotal);

        // Default language set to Arabic as per request
        let currentLang = 'ar';
        // This will hold the loaded translations from the external files
        let currentTranslations = {};

        // Function to calculate and update the total
        function updateTotal() {
            let total = 0;
            const quantityInputs = document.querySelectorAll('input[type="number"][data-price]');
            const totalAmountSpan = document.getElementById('total-amount');

            quantityInputs.forEach(input => {
                const price = parseFloat(input.dataset.price);
                const quantity = parseFloat(input.value);
                if (!isNaN(price) && !isNaN(quantity) && quantity >= 0) {
                    total += price * quantity;
                }
            });

            // Apply highlight animation
            totalAmountSpan.classList.remove('total-highlight');
            void totalAmountSpan.offsetWidth; // Trigger reflow to restart animation
            totalAmountSpan.classList.add('total-highlight');

            // Get current currency based on loaded translations
            const currency = currentTranslations.currencySymbol;

            totalAmountSpan.textContent = total.toFixed(2) + " " + currency;
        }

        // Function to load the translation file dynamically
        async function loadTranslations(lang) {
            return new Promise((resolve, reject) => {
                const scriptId = 'translation-script';
                const existingScript = document.getElementById(scriptId);
                if (existingScript) {
                    existingScript.remove(); // Remove existing script to load new one
                }

                const script = document.createElement('script');
                script.id = scriptId;
                script.src = `storage/app/public/${lang}.js`; // Assuming files are in the same directory as this HTML
                script.onload = () => {
                    // Assign the correct global translation object based on the loaded script
                    if (lang === 'en') {
                        currentTranslations = translations_en;
                    } else if (lang === 'ar') {
                        currentTranslations = translations_ar;
                    }
                    resolve();
                };
                script.onerror = () => reject(new Error(`Failed to load ${lang}.js`));
                document.head.appendChild(script);
            });
        }

        // Function to update content based on language
        async function updateContent(lang) {
            currentLang = lang; // Update currentLang global variable
            await loadTranslations(lang); // Load the specific language translations

            // Now currentTranslations is populated, proceed with updating UI
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
                // Update active language button styling (though buttons are hidden, this keeps logic consistent)
                const langArButton = document.getElementById('lang-ar');
                const langEnButton = document.getElementById('lang-en');
                if (langArButton) { // Check if button exists before modifying
                    langArButton.classList.remove('bg-gray-200', 'text-gray-700');
                    langArButton.classList.add('bg-purple-500', 'text-white');
                }
                if (langEnButton) { // Check if button exists before modifying
                    langEnButton.classList.remove('bg-purple-500', 'text-white');
                    langEnButton.classList.add('bg-gray-200', 'text-gray-700');
                }
            } else {
                body.classList.remove('rtl');
                html.removeAttribute('dir');
                // Update active language button styling (though buttons are hidden, this keeps logic consistent)
                const langEnButton = document.getElementById('lang-en');
                const langArButton = document.getElementById('lang-ar');
                if (langEnButton) { // Check if button exists before modifying
                    langEnButton.classList.remove('bg-gray-200', 'text-gray-700');
                    langEnButton.classList.add('bg-purple-500', 'text-white');
                }
                if (langArButton) { // Check if button exists before modifying
                    langArButton.classList.remove('bg-purple-500', 'text-gray-700');
                    langArButton.classList.add('bg-gray-200', 'text-gray-700');
                }
            }

            // Apply/remove RTL specific classes for quantity controls
            document.querySelectorAll('.quantity-controls').forEach(control => {
                if (lang === 'ar') {
                    control.classList.add('rtl');
                } else {
                    control.classList.remove('rtl');
                }
            });

            // Apply/remove RTL specific classes for product details text alignment
            document.querySelectorAll('.product-details-grow').forEach(detail => {
                if (lang === 'ar') {
                    detail.classList.remove('sm:text-left'); // Remove LTR-specific small screen left align
                    detail.classList.add('sm:text-right', 'text-right'); // Add RTL-specific small screen right align, and general mobile right
                    detail.classList.remove('text-center'); // Remove default mobile center
                } else {
                    detail.classList.remove('sm:text-right', 'text-right'); // Remove RTL-specific small screen right align, and general mobile right
                    detail.classList.add('sm:text-left', 'text-center'); // Add LTR-specific small screen left align, and re-apply default mobile center
                }
            });

            // Dynamically update product list using productsData
            const productListElement = document.getElementById('product-list');
            productListElement.innerHTML = ''; // Clear existing list (and loading message)

            if (productsData.length === 0) {
                const noProductsMessage = document.createElement('li');
                noProductsMessage.className = 'text-gray-600 text-center py-4';
                noProductsMessage.textContent = currentTranslations.noProductsToDisplay;
                productListElement.appendChild(noProductsMessage);
                document.getElementById('checkout-btn').disabled = true; // Disable checkout if cart is empty
                return;
            } else {
                document.getElementById('checkout-btn').disabled = false; // Enable checkout if items exist
            }

            productsData.forEach(product => {
                // Use 'id' for productIdSlug, fallback to name if id is null
                const productIdSlug = (product.id ? product.id.toString() : product.name.toLowerCase().replace(/\s/g, '-').replace(/[^a-z0-9-]/g, ''));

                // Use the 'name' property directly as it comes from $checkoutItems, which is already translated or in a consistent format
                const productName = product.name;
                const productPrice = product.price_per_kg.toFixed(2); // Use price_per_kg for display
                const initialQuantity = product.quantity.toFixed(1); // Get initial quantity from cart data

                // Combined price and unit for display
                const priceAndUnit = `${productPrice} ${currentTranslations.currencySymbol} / ${currentTranslations.unitName}`;

                const listItem = document.createElement('li');
                listItem.className = 'flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 fade-in';
                listItem.style.animationDelay = `${(productsData.indexOf(product) * 0.1) + 0.1}s`; // Stagger animation

                listItem.innerHTML = `
                    <div class="flex-grow mb-2 sm:mb-0 sm:mr-4 product-details-grow ${lang === 'ar' ? 'text-right sm:text-right' : 'text-center sm:text-left'}">
                        <span class="text-lg font-medium text-gray-700">${productName}</span>
                        <p class="text-sm text-gray-500">${priceAndUnit}</p>
                    </div>
                    <div class="flex items-center space-x-2 quantity-controls ${lang === 'ar' ? 'rtl' : ''}">
                        <button class="quantity-btn p-2 bg-purple-200 text-purple-800 rounded-md hover:bg-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200 w-8 h-8 flex items-center justify-center text-xl font-bold" data-action="decrement" data-target="qty-${productIdSlug}">-</button>
                        <label for="qty-${productIdSlug}" class="sr-only">Quantity for ${productName}</label>
                        <input type="number" id="qty-${productIdSlug}" data-product-id="${product.id}" data-price="${product.price_per_kg}" value="${initialQuantity}" min="0" step="1" class="w-20 p-2 border border-gray-300 rounded-md text-center focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-200">
                        <button class="quantity-btn p-2 bg-purple-200 text-purple-800 rounded-md hover:bg-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200 w-8 h-8 flex items-center justify-center text-xl font-bold" data-action="increment" data-target="qty-${productIdSlug}">+</button>
                        <span class="text-gray-700 font-medium unit-display">${currentTranslations.unitName}</span>
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
            let currentValue = parseFloat(input.value);

            if (action === 'increment') {
                input.value = (currentValue + 1).toFixed(1);
            } else if (action === 'decrement') {
                if (currentValue > 0) {
                    input.value = (currentValue - 1).toFixed(1);
                    if (parseFloat(input.value) < 0) {
                        input.value = "0.0";
                    }
                }
            }
            updateTotal();
        }

        // Checkout Button Logic for Laravel Backend
        document.getElementById('checkout-btn').addEventListener('click', async () => {
            const selectedItems = [];
            document.querySelectorAll('input[type="number"][data-price]').forEach(input => {
                const quantity = parseFloat(input.value);
                if (quantity > 0) {
                    const productId = input.dataset.productId;
                    const price = parseFloat(input.dataset.price);

                    // Find the product name by going up to the list item and finding the span
                    const productNameElement = input.closest('li').querySelector('.product-details-grow span');
                    const productName = productNameElement ? productNameElement.textContent : productId;

                    selectedItems.push({
                        type:'scanner',
                        id: productId,
                        name: productName,
                        quantity: quantity,
                        price_per_kg: price
                    });
                }
            });

            if (selectedItems.length === 0) {
                // Using a custom message box instead of alert()
                showCustomMessageBox("Please select at least one item to proceed to checkout.", "Info");
                return;
            }

            console.log("Items to checkout:", selectedItems);

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
                        showCustomMessageBox("Something went wrong with the redirect.", "Error");
                    }
                } else {
                    const errorData = await response.json();
                    showCustomMessageBox(`Checkout failed: ${errorData.message || 'Unknown error'}`, "Error");
                    console.error('Checkout error:', errorData);
                }
            } catch (error) {
                console.error('Network error or unexpected:', error);
                showCustomMessageBox("An error occurred. Please try again.", "Error");
            }
        });

        // Custom Message Box functions
        function showCustomMessageBox(message, type = 'Info') {
            const messageBox = document.createElement('div');
            messageBox.className = `fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50`;
            messageBox.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full text-center">
                    <h3 class="text-xl font-bold mb-4 ${type === 'Error' ? 'text-red-600' : 'text-gray-800'}">${type}</h3>
                    <p class="text-gray-700 mb-6">${message}</p>
                    <button id="close-message-box" class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400">OK</button>
                </div>
            `;
            document.body.appendChild(messageBox);

            document.getElementById('close-message-box').addEventListener('click', () => {
                document.body.removeChild(messageBox);
            });
        }


        // Event listeners for language buttons (kept for potential future re-enabling, but won't be triggered if buttons are hidden)
        document.getElementById('lang-en').addEventListener('click', () => updateContent('en'));
        document.getElementById('lang-ar').addEventListener('click', () => updateContent('ar'));

        // Initial content load and fetch products
        document.addEventListener('DOMContentLoaded', () => {
            updateContent(currentLang); // Now currentLang is explicitly 'ar'
        });
    </script>

</body>
</html>

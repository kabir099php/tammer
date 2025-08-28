<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">{{$store->name}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #DBDAD6; /* Changed from #f3f4f6 */
        }

        .fade-in { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .delay-100 { animation-delay: 0.1s; } .delay-200 { animation-delay: 0.2s; } .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; } .delay-500 { animation-delay: 0.5s; } .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; } .delay-800 { animation-delay: 0.8s; } .delay-900 { animation-delay: 0.9s; }
        .delay-1000 { animation-delay: 1s; }

        body.rtl { direction: rtl; font-family: 'Noto Sans Arabic', sans-serif; }
        body.rtl .text-right-on-rtl { text-align: right; }
        body.rtl .text-left-on-rtl { text-align: left; }
        body.rtl .rtl-icon-contact, body.rtl .rtl-icon-location { margin-right: 0; margin-left: 0.5rem; }
        body.rtl .heading-rtl-align { text-align: right; }

        body { min-height: 100vh; display: block; overflow-y: auto; }
        .main-container { min-height: auto; height: auto; flex-direction: column; margin: 0; }
        @media (min-width: 768px) {
            body { display: flex; align-items: center; justify-content: center; overflow: hidden; }
            .main-container { height: 90vh; max-height: 700px; flex-direction: row; margin-left: auto; margin-right: auto; }
            .md\:h-full { height: 100%; }
            .flex-grow { overflow-y: auto; }
        }

        .star-rating { display: flex; align-items: center; justify-content: center; }
        .star { color: #EDAA4B; /* Star color changed */ font-size: 1.5rem; margin: 0 0.1rem; }
        body.rtl .star-rating { flex-direction: row-reverse; }
       
    .scroll-container {
        max-height: 400px; /* Adjust as needed */
        overflow: hidden; /* Hide the scrollbar */
         overflow-y: hidden;
        position: relative;
    }

    .scroll-content {
        /* This div will be animated */
        display: flex;
        flex-direction: column;
    }

    /* Animation definition for continuous scroll */
    @keyframes scroll-up {
        to {
            transform: translateY(-50%);
        }
    }

    /* Marquee Styles */
    .marquee-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #DBDAD6;
        padding: 5px 0;
        overflow: hidden;
        white-space: nowrap;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    }
    .marquee-text {
        display: inline-block;
        font-family: 'Noto Sans Arabic', sans-serif;
        animation: marquee 30s linear infinite;
        font-size: 0.75rem; /* text-xs */
        font-weight: 600; /* font-semibold */
        color: #000; /* black font */
    }
    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    </style>
</head>
<?php
        $name = $store->vendor_name_ar; // Or whatever your default is or passed from controller
        
        $contact = $store->phone; 
        $vendor = \App\Models\Vendor::where('id',$store->vendor_id)->first();
        $crn = $vendor->crn;
        $detail_page_footer = $vendor->detail_page_footer;
        $currency = $currency;
        $storeId = $store->id ?? 'defaultStoreId'; 
        $storeName = $store->name ?? 'defaultStoreId'; 
    ?>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-[#DBDAD6] to-[#DBDAD6]"> 
    <div class="main-container w-full bg-white rounded-xl shadow-2xl overflow-hidden md:flex transform transition-all duration-300 hover:scale-[1.005]">
        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0" style="display:none">
            <button id="lang-en" class="px-4 py-2 bg-[#EDAA4B] text-white rounded-lg shadow-md hover:bg-[#D4933F] focus:outline-none focus:ring-2 focus:ring-[#D4933F] focus:ring-opacity-75 transition duration-200">EN</button> 
            <button id="lang-ar" class="px-4 py-2 bg-[#B0ACA7] text-gray-700 rounded-lg shadow-md hover:bg-[#9B9994] focus:outline-none focus:ring-2 focus:ring-[#9B9994] focus:ring-opacity-75 transition duration-200">AR</button> 
        </div>

        <div class="p-6 sm:p-8 flex flex-col md:w-1/2 lg:w-2/5 justify-between bg-gradient-to-br from-[#838484] to-[#606060] text-white relative rounded-t-xl md:rounded-l-xl md:rounded-tr-none fade-in delay-100"> 
            <div>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex-shrink-0">
                        @if($store->logo)
                        <img src="{{url('/storage/app/public/store/')}}/{{$store->logo}}" style=" max-width:50px">
                        @else
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-inner">O</div>
                        @endif
                        
                    </div>
                    <div>
                        <h1 style="margin-right:10px; font-size: 1.5rem;" class="text-3xl sm:text-4xl font-extrabold mb-1" data-key="sellerName" ></h1>
                        <p style="margin-right:10px" class="text-[#EDAA4B] text-lg" data-key="sellerTagline"></p> 
                    </div>
                </div>

                <div class="mb-8">
                    <p class="text-lg text-white mb-2 flex items-center" id="contact-row">
                        <svg class="w-5 h-5 text-[#EDAA4B] rtl-icon-contact" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.774a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg> <span data-key="contactLabel"></span> <span class="font-semibold" data-key="contactNumber"></span>
                    </p>
                    <p class="text-lg text-white mb-2 flex items-center" id="location-row">
                        <svg class="w-5 h-5 text-[#EDAA4B] rtl-icon-location" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg> <span data-key="locationLabel"></span> <span class="font-semibold" data-key="locationValue"></span>
                    </p>

                    <p class="text-lg text-white mb-2 flex items-center" id="cr-row">
                        <svg class="w-5 h-5 text-[#EDAA4B] rtl-icon-location" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <path d="M10 2a8 8 0 00-8 8c0 2.45 1.135 4.743 3 6.326V20l5-2.5 5 2.5v-1.674c1.865-1.583 3-3.876 3-6.326a8 8 0 00-8-8zM7 9a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2zm-3 6a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                        <span data-key="crLabel"></span> <span class="font-semibold" data-key="crValue"></span>
                    </p>
                </div>

                </div>

            <div class="text-center mt-auto fade-in delay-300">
                <h2 class="text-2xl font-bold text-white mb-4 border-b-2 border-[#EDAA4B] pb-2 heading-rtl-align" data-key="qrCodeHeading"></h2> <div class="bg-white p-6 rounded-lg inline-block shadow-lg border-2 border-[#EDAA4B]" style="padding: 1rem !important"> <div id="qrcode" class="w-44 h-44 rounded-md mx-auto transform transition-transform duration-300 hover:scale-105" style="height:10rem !important;"></div>
                    <p class="mt-4 text-[#838484] text-sm" data-key="qrCodeDescription"></p> 
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 flex flex-col md:w-1/2 lg:w-3/5 bg-white rounded-b-xl md:rounded-r-xl md:rounded-bl-none relative">
            <div class="mb-8 fade-in delay-200 flex-grow overflow-y-auto">
                <h2 class="text-3xl font-bold text-[#838484] mb-6 border-b-2 border-[#EDAA4B] pb-2 heading-rtl-align" data-key="productPricesHeading"></h2> 
                <div class="mb-8 fade-in delay-200 flex-grow scroll-container">
                <ul id="product-list" class="space-y-4 scroll-content"></ul>
                </div>
                </div>
            
            <div class="absolute bottom-4 left-4 z-10 flex items-center flex-row">
                <span class="text-xs text-gray-500 font-semibold ml-2">تحت إدارة</span>
    <a href="https://riyadh.dev" target="_blank" class="flex-shrink-0">
        <img src="https://riyadh.dev/wp-content/uploads/2024/11/logo.svg" alt="Powered by Riyadh.dev" class="h-8 w-auto">
    </a>

    
</div>
        </div>
    </div>
    <div class="marquee-container">
        <div class="marquee-text">
            <span>&nbsp;&nbsp;</span>
            <span data-key="marqueeMessage"></span>
            <span>&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>
    
    <script src="https://unpkg.com/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        const name = @json($name);
        const contact = @json($contact);
        const storeId = @json($storeId);
        const storeName = @json($storeName);
        const currency = @json($currency);
        const crn = @json($crn);
        const detail_page_footer = @json($detail_page_footer);
        

        let productsData = [];
        let currentLang = 'ar';

        const translations = {
            en: {
                pageTitle: storeName,
                sellerName: storeName,
                sellerTagline: "",
                contactLabel: "Contact:",
                contactNumber: contact,
                locationLabel: "Location:",
                locationValue: "Riyadh",
                // overallSatisfaction: "Overall Satisfaction",
                // positivePercentage: "100% Positive",
                // basedOn: "Based on",
                // reviewCount: "233 Reviews",
                productPricesHeading: "Product Prices",
                currencyUnit: "SAR/kg",
                qrCodeHeading: "Scan for More Details",
                qrCodeDescription: "Scan this QR code to view the full product catalog and order.",
                loadingProducts: "Loading products...",
                errorLoadingProducts: "Failed to load products. Please try again later.",
                crLabel: "CR Number :",
                crValue: crn,
                noProductsToDisplay: "No products to display.",
                marqueeMessage: detail_page_footer
            },
            "ar": {
                pageTitle: storeName,
                sellerName: storeName, // Using 'فارس' for Arabic name as a placeholder, adjust if $name includes Arabic
                sellerTagline: "",
                contactLabel: "جهة الاتصال:",
                contactNumber: contact,
                locationLabel: "الموقع:",
                locationValue: "الرياض-تعمير",
                // overallSatisfaction: "التقييمات",
                // positivePercentage: "100% إيجابي",
                // basedOn: "بناءً على",
                // reviewCount: "233 مراجعة",
                productPricesHeading: "أسعار المنتجات",
                currencyUnit:  currency + " /كجم",
                qrCodeHeading: "امسح للحصول على مزيد من التفاصيل",
                qrCodeDescription: "امسح رمز الاستجابة السريعة هذا لعرض كتالوج المنتجات الكامل والطلب.",
                loadingProducts: "جارٍ تحميل المنتجات...",
                errorLoadingProducts: "فشل تحميل المنتجات. الرجاء المحاولة مرة أخرى لاحقًا.",
                crLabel: "السجل التجاري :",
                crValue: crn,
                noProductsToDisplay: "لا توجد منتجات لعرضها.",
                marqueeMessage: detail_page_footer
            }
        };

    function startAutoScroll() {
    const scrollContainer = document.querySelector('.scroll-container');
    const scrollContent = document.querySelector('.scroll-content');
    if (!scrollContainer || !scrollContent) return;

    const speed = 0.5; // Controls the scroll speed

    function animateScroll() {
        const containerHeight = scrollContainer.clientHeight;
        const contentHeight = scrollContent.scrollHeight;

        // Only start scrolling if content is larger than the container
        if (contentHeight <= containerHeight) {
            requestAnimationFrame(animateScroll);
            return;
        }

        // Check if we hit the bottom
        if (scrollContainer.scrollTop + containerHeight >= contentHeight) {
            scrollContainer.scrollTop = 0; // Reset to the top
        } else {
            scrollContainer.scrollTop += speed; // Continue scrolling down
        }

        requestAnimationFrame(animateScroll);
    }

    // Wait for products to render before starting animation
    setTimeout(() => {
        animateScroll();
    }, 1000);
}

        function renderStarRating(rating) {
            const starRatingContainer = document.querySelector('#satisfaction-left .star-rating');
            if (!starRatingContainer) return;
            starRatingContainer.innerHTML = '';

            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;

            for (let i = 0; i < fullStars; i++) {
                const star = document.createElement('span');
                star.className = 'star';
                star.innerHTML = '&#9733;';
                starRatingContainer.appendChild(star);
            }

            if (hasHalfStar) {
                const halfStar = document.createElement('span');
                halfStar.className = 'star';
                halfStar.innerHTML = '&#9733;';
                halfStar.style.clipPath = 'inset(0 50% 0 0)';
                halfStar.style.display = 'inline-block';
                halfStar.style.overflow = 'hidden';
                starRatingContainer.appendChild(halfStar);
            }

            const totalStars = 5;
            const filledStars = Math.ceil(rating);
            const emptyStars = totalStars - filledStars;

            for (let i = 0; i < emptyStars; i++) {
                const emptyStar = document.createElement('span');
                emptyStar.className = 'star';
                emptyStar.style.color = '#ccc';
                emptyStar.innerHTML = '&#9733;';
                starRatingContainer.appendChild(emptyStar);
            }
        }

        async function fetchProducts() {
            const productListElement = document.getElementById('product-list');
            productListElement.innerHTML = `<li id="loading-message" class="text-gray-500 text-center py-4">${translations[currentLang].loadingProducts}</li>`;

            try {
                const response = await fetch(`https://trymajlis.com/api/v1/items/get-products-demo?store_id=${storeId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const productApiResponse = await response.json();

                productsData = productApiResponse.map(item => ({
                    id: item.id,
                    name_en: item.translations.find(t => t.locale === 'en' && t.key === 'name')?.value || item.name,
                    name_ar: item.translations.find(t => t.locale === 'ar' && t.key === 'name')?.value || item.name,
                    price_per_kg: item.price
                }));

                updateContent(currentLang);
            } catch (error) {
                console.error("Error fetching products:", error);
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.textContent = translations[currentLang].errorLoadingProducts;
                    loadingMessage.style.color = 'red';
                }
            }
        }

        function updateContent(lang) {
            currentLang = lang;
            const currentTranslations = translations[lang];

            document.title = currentTranslations.pageTitle;

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

                document.querySelectorAll('.rtl-icon-contact, .rtl-icon-location').forEach(icon => {
                    icon.classList.remove('mr-2');
                    icon.classList.add('ml-2');
                });

                document.querySelector('[data-key="contactNumber"]').classList.remove('ml-1');
                document.querySelector('[data-key="contactNumber"]').classList.add('mr-1');
                document.querySelector('[data-key="locationValue"]').classList.remove('ml-1');
                document.querySelector('[data-key="locationValue"]').classList.add('mr-1');
                document.querySelector('[data-key="crValue"]').classList.remove('ml-1');
                document.querySelector('[data-key="crValue"]').classList.add('mr-1');

                // document.getElementById('satisfaction-left').classList.remove('text-left');
                // document.getElementById('satisfaction-left').classList.add('text-right');
                // document.getElementById('satisfaction-right').classList.remove('text-right');
                // document.getElementById('satisfaction-right').classList.add('text-left');
            } else {
                body.classList.remove('rtl');
                html.removeAttribute('dir');

                document.querySelectorAll('.rtl-icon-contact, .rtl-icon-location').forEach(icon => {
                    icon.classList.remove('ml-2');
                    icon.classList.add('mr-2');
                });

                document.querySelector('[data-key="contactNumber"]').classList.remove('mr-1');
                document.querySelector('[data-key="contactNumber"]').classList.add('ml-1');
                document.querySelector('[data-key="locationValue"]').classList.remove('mr-1');
                document.querySelector('[data-key="locationValue"]').classList.add('ml-1');
                document.querySelector('[data-key="crValue"]').classList.remove('mr-1');
                document.querySelector('[data-key="crValue"]').classList.add('ml-1');

                document.getElementById('satisfaction-left').classList.remove('text-right');
                document.getElementById('satisfaction-left').classList.add('text-left');
                document.getElementById('satisfaction-right').classList.remove('text-left');
                document.getElementById('satisfaction-right').classList.add('text-right');
            }

            // Language button styling
            document.getElementById('lang-en').classList.remove('bg-[#EDAA4B]', 'text-white');
            document.getElementById('lang-en').classList.add('bg-[#B0ACA7]', 'text-gray-700');
            document.getElementById('lang-ar').classList.remove('bg-[#EDAA4B]', 'text-white');
            document.getElementById('lang-ar').classList.add('bg-[#B0ACA7]', 'text-gray-700');

            if (lang === 'en') {
                document.getElementById('lang-en').classList.remove('bg-[#B0ACA7]', 'text-gray-700');
                document.getElementById('lang-en').classList.add('bg-[#EDAA4B]', 'text-white');
            } else if (lang === 'ar') {
                document.getElementById('lang-ar').classList.remove('bg-[#B0ACA7]', 'text-gray-700');
                document.getElementById('lang-ar').classList.add('bg-[#EDAA4B]', 'text-white');
            }

           
        const productListElement = document.getElementById('product-list');
        productListElement.innerHTML = '';

        if (productsData.length === 0) {
            const noProductsMessage = document.createElement('li');
            noProductsMessage.className = 'text-[#838484] text-center py-4';
            noProductsMessage.textContent = currentTranslations.noProductsToDisplay;
            productListElement.appendChild(noProductsMessage);
            return;
        }

        // 1. Render the list twice to create the seamless loop
        const renderProducts = (targetElement) => {
            productsData.forEach(product => {
                const listItem = document.createElement('li');
                listItem.className = 'flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 hover:bg-[#B0ACA7]';
                listItem.style.flexShrink = 0;

                const productNameSpan = document.createElement('span');
                productNameSpan.className = 'text-lg font-medium text-[#838484] product-name';
                productNameSpan.textContent = lang === 'ar' ? product.name_ar : product.name_en;

                const productPriceSpan = document.createElement('span');
                productPriceSpan.className = 'text-xl font-bold text-[#EDAA4B] product-price';
                productPriceSpan.textContent = `${product.price_per_kg.toFixed(2)} ${currentTranslations.currencyUnit}`;

                listItem.appendChild(productNameSpan);
                listItem.appendChild(productPriceSpan);
                targetElement.appendChild(listItem);
            });
        };

        renderProducts(productListElement);
        renderProducts(productListElement); // This is the crucial duplication

        // 2. Start the continuous scroll animation after rendering
        startContinuousScroll();
        }
        

        function startContinuousScroll() {
        const scrollContent = document.querySelector('.scroll-content');
        if (!scrollContent) return;

        // Calculate the duration based on the content height for a consistent speed
        // The total scroll height is the combined height of both lists
        const totalContentHeight = scrollContent.scrollHeight;
        
        // This calculates the duration in seconds. A higher number makes it slower.
        const scrollSpeed = 50; // pixels per second
        const duration = totalContentHeight / scrollSpeed;

        // Apply the animation with the calculated duration
        scrollContent.style.animationDuration = `${duration}s`;
    }

        // Function to generate the QR code
        function generateQrCode(data) {
            const qrcodeContainer = document.getElementById('qrcode');
            qrcodeContainer.innerHTML = ''; // Clear any existing QR code

            // Ensure QRCode is available before trying to use it
            if (typeof QRCode !== 'undefined') {
                new QRCode(qrcodeContainer, {
                    text: data,
                    width: 160,
                    height: 160,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                console.error("QRCode library not loaded.");
            }
        }

        document.getElementById('lang-en').addEventListener('click', () => updateContent('en'));
        document.getElementById('lang-ar').addEventListener('click', () => updateContent('ar'));

        document.addEventListener('DOMContentLoaded', () => {
            updateContent(currentLang);
            fetchProducts();

            // Define the data for your QR code. This is where you'd put the dynamic URL.
            const qrCodeData = `https://waslqr.com/checkout?store_id=${storeId}`; 
            // IMPORTANT: Replace 'https://your-domain.com/products/catalog/' with your actual base URL for the product catalog.

            generateQrCode(qrCodeData);
             requestAnimationFrame(() => {
            startAutoScroll();
        });
        });
    </script>
</body>
</html>
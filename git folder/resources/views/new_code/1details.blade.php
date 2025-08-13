<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">Seller Details - Faris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for animations and font */
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
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
        .delay-800 { animation-delay: 0.8s; }
        .delay-900 { animation-delay: 0.9s; }
        .delay-1000 { animation-delay: 1s; }

        /* RTL specific styles */
        body.rtl {
            direction: rtl; /* Sets overall text direction to RTL */
            font-family: 'Noto Sans Arabic', sans-serif; /* Apply Arabic font */
        }

        /* Ensure text alignment for headings and specific blocks */
        body.rtl .text-right-on-rtl {
            text-align: right;
        }

        body.rtl .text-left-on-rtl {
            text-align: left;
        }

        /* Adjustments for icons in RTL - when parent is flex-row-reverse */
        body.rtl .rtl-icon-contact,
        body.rtl .rtl-icon-location {
            margin-right: 0; /* Remove default mr-2 */
            margin-left: 0.5rem; /* Add margin-left to push text away from icon */
        }

        /* Text alignment for headings within sections when RTL is active */
        body.rtl .heading-rtl-align {
            text-align: right; /* Ensure headings are right-aligned in RTL */
        }

        /* --- Custom adjustments for scrollability --- */
        /* For mobile (default), allow scrolling */
        body {
            min-height: 100vh; /* Ensure body takes at least full viewport height */
            display: block; /* Override flex on body for mobile */
            overflow-y: auto; /* Allow vertical scrolling */
        }

        .main-container {
            min-height: auto; /* Let content dictate height on mobile */
            height: auto; /* Override fixed height */
            flex-direction: column; /* Stack columns on mobile */
            margin: 0; /* Remove mx-auto for mobile */
        }

        /* For medium screens and up, revert to original layout */
        @media (min-width: 768px) {
            body {
                display: flex; /* Re-enable flex for centering on desktop */
                align-items: center;
                justify-content: center;
                overflow: hidden; /* Prevent body scroll on desktop if container handles overflow */
            }

            .main-container {
                height: 90vh; /* Set a percentage height for the card on desktop */
                max-height: 700px; /* Optional: max height for very large screens */
                flex-direction: row; /* Side-by-side columns */
                margin-left: auto; /* Re-enable mx-auto */
                margin-right: auto; /* Re-enable mx-auto */
            }

            .md\:h-full { /* Ensure nested containers take full height of parent on desktop */
                height: 100%;
            }

            .flex-grow { /* Ensure the product list grows to fill space and enables its own scroll */
                overflow-y: auto;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-50 to-purple-50">

    <div class="main-container w-full bg-white rounded-xl shadow-2xl overflow-hidden md:flex transform transition-all duration-300 hover:scale-[1.005]">

        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0">
            <button id="lang-en" class="px-4 py-2 bg-purple-500 text-white rounded-lg shadow-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200">
                EN
            </button>
            <button id="lang-ar" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200">
                AR
            </button>
        </div>


        <div class="p-6 sm:p-8 flex flex-col md:w-1/2 lg:w-2/5 justify-between bg-gradient-to-br from-purple-600 to-indigo-700 text-white relative rounded-t-xl md:rounded-l-xl md:rounded-tr-none fade-in delay-100">
            <div>
                <!-- Seller Name and Tagline -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-inner">F</div>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold mb-1" data-key="sellerName">Faris</h1>
                        <p class="text-indigo-200 text-lg" data-key="sellerTagline">Trusted Fresh Produce Seller</p>
                    </div>
                </div>

                <!-- Contact and Location Details -->
                <div class="mb-8">
                    <!-- Contact Number -->
                    <p class="text-lg text-white mb-2 flex items-center flex-row-reverse-on-rtl">
                        <svg class="w-5 h-5 mr-2 text-indigo-200 rtl-icon-contact" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.774a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                        <span data-key="contactLabel">Contact:</span> <span class="font-semibold ml-1" data-key="contactNumber">*** **** 8765</span>
                    </p>
                    <!-- Location -->
                    <p class="text-lg text-white mb-2 flex items-center flex-row-reverse-on-rtl">
                        <svg class="w-5 h-5 mr-2 text-indigo-200 rtl-icon-location" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        <span data-key="locationLabel">Location:</span> <span class="font-semibold ml-1" data-key="locationValue">Riyad, Saudi Arabia</span>
                    </p>
                </div>

                <!-- Overall Satisfaction -->
                <div class="bg-white bg-opacity-20 p-4 rounded-lg flex items-center justify-between shadow-md mb-8">
                    <div class="text-white text-left-on-rtl">
                        <p class="text-sm" data-key="overallSatisfaction">Overall Satisfaction</p>
                        <p class="text-2xl font-bold" data-key="positivePercentage">100% Positive</p>
                    </div>
                    <div class="text-right text-right-on-rtl">
                        <p class="text-sm" data-key="basedOn">Based on</p>
                        <p class="text-2xl font-bold" data-key="reviewCount">233 Reviews</p>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="text-center mt-auto fade-in delay-300">
                <h2 class="text-2xl font-bold text-white mb-4 border-b-2 border-indigo-300 pb-2 heading-rtl-align" data-key="qrCodeHeading">Scan for More Details</h2>
                <div class="bg-white p-6 rounded-lg inline-block shadow-lg border-2 border-purple-300">
                    <img src="{{url('public/assets/qr.svg')}}" alt="QR Code" class="w-44 h-44 rounded-md mx-auto transform transition-transform duration-300 hover:scale-105">
                    <p class="mt-4 text-gray-600 text-sm" data-key="qrCodeDescription">Scan this QR code to view the full product catalog and order.</p>
                </div>
            </div>
        </div>

        <!-- Product Prices Section -->
        <div class="p-6 sm:p-8 flex flex-col md:w-1/2 lg:w-3/5 bg-white rounded-b-xl md:rounded-r-xl md:rounded-bl-none">
            <div class="mb-8 fade-in delay-200 flex-grow overflow-y-auto">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b-2 border-purple-200 pb-2 heading-rtl-align" data-key="productPricesHeading">Product Prices</h2>
                <ul class="space-y-4">
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <span class="text-lg font-medium text-gray-700" data-key="bloodyOrange">Bloody orange</span>
                        <span class="text-xl font-bold text-purple-700" data-key="bloodyOrangePrice">20.00 SAR/kg</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <span class="text-lg font-medium text-gray-700" data-key="liverPeach">Liver peach</span>
                        <span class="text-xl font-bold text-purple-700" data-key="liverPeachPrice">16.00 SAR/kg</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <span class="text-lg font-medium text-gray-700" data-key="apples">Apples</span>
                        <span class="text-xl font-bold text-purple-700" data-key="applesPrice">16.00 SAR/kg</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <span class="text-lg font-medium text-gray-700" data-key="solarPeach">Solar peach</span>
                        <span class="text-xl font-bold text-purple-700" data-key="solarPeachPrice">20.00 SAR/kg</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <span class="text-lg font-medium text-gray-700" data-key="watermelon">Watermelon</span>
                        <span class="text-xl font-bold text-purple-700" data-key="watermelonPrice">12.00 SAR/kg</span>
                    </li>
                   
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Translations object
        const translations = {
            en: {
                pageTitle: "Seller Details - Faris",
                sellerName: "Faris",
                sellerTagline: "Trusted Fresh Produce Seller",
                contactLabel: "Contact:",
                contactNumber: "*** **** 8765",
                locationLabel: "Location:",
                locationValue: "Riyad",
                overallSatisfaction: "Overall Satisfaction",
                positivePercentage: "100% Positive",
                basedOn: "Based on",
                reviewCount: "233 Reviews",
                productPricesHeading: "Product Prices",
                bloodyOrange: "Bloody orange",
                bloodyOrangePrice: "20.00 SAR/kg",
                liverPeach: "Liver peach",
                liverPeachPrice: "16.00 SAR/kg",
                apples: "Apples",
                applesPrice: "16.00 SAR/kg",
                solarPeach: "Solar peach",
                solarPeachPrice: "20.00 SAR/kg",
                watermelon: "Watermelon",
                watermelonPrice: "12.00 SAR/kg",
                fantasyQueenFruit: "Fantasy queen fruit",
                fantasyQueenFruitPrice: "16.00 SAR/kg",
                leanPeach: "Lean peach",
                leanPeachPrice: "16.00 SAR/kg",
                qrCodeHeading: "Scan for More Details",
                qrCodeDescription: "Scan this QR code to view the full product catalog and order."
            },
            "ar": {
                pageTitle: "تفاصيل البائع - فارس",
                sellerName: "فارس",
                sellerTagline: "بائع خضروات وفواكه طازجة موثوق به",
                contactLabel: "جهة الاتصال:",
                contactNumber: "*** **** 8765", // Numbers are typically displayed LTR even in RTL context
                locationLabel: "الموقع:",
                locationValue: "الرياض",
                overallSatisfaction: "الرضا العام",
                positivePercentage: "100% إيجابي",
                basedOn: "بناءً على",
                reviewCount: "233 مراجعة",
                productPricesHeading: "أسعار المنتجات",
                bloodyOrange: "برتقال دموي",
                bloodyOrangePrice: "20.00 ريال سعودي/كجم",
                liverPeach: "خوخ الكبد",
                liverPeachPrice: "16.00 ريال سعودي/كجم",
                apples: "تفاح",
                applesPrice: "16.00 ريال سعودي/كجم",
                solarPeach: "خوخ الشمس",
                solarPeachPrice: "20.00 ريال سعودي/كجم",
                watermelon: "بطيخ",
                watermelonPrice: "12.00 ريال سعودي/كجم",
                fantasyQueenFruit: "فاكهة ملكة الخيال",
                fantasyQueenFruitPrice: "16.00 ريال سعودي/كجم",
                leanPeach: "خوخ نحيل",
                leanPeachPrice: "16.00 ريال سعودي/كجم",
                qrCodeHeading: "امسح للحصول على مزيد من التفاصيل",
                qrCodeDescription: "امسح رمز الاستجابة السريعة هذا لعرض كتالوج المنتجات الكامل والطلب."
            }
        };

        // Function to update content based on language
        function updateContent(lang) {
            const currentTranslations = translations[lang];
            for (const key in currentTranslations) {
                const element = document.querySelector(`[data-key="${key}"]`);
                if (element) {
                    element.textContent = currentTranslations[key];
                }
            }

            const body = document.body;
            const html = document.documentElement; // Get the HTML element for dir attribute

            if (lang === 'ar') {
                body.classList.add('rtl');
                html.setAttribute('dir', 'rtl'); // Set dir attribute for proper RTL handling
            } else {
                body.classList.remove('rtl');
                html.removeAttribute('dir'); // Remove dir attribute
            }

            // Update active language button styling
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
        }

        // Event listeners for language buttons
        document.getElementById('lang-en').addEventListener('click', () => updateContent('en'));
        document.getElementById('lang-ar').addEventListener('click', () => updateContent('ar'));

        // Initial content load (default to English)
        document.addEventListener('DOMContentLoaded', () => {
            updateContent('en');
        });
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">Payment Failed</title>
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
            opacity: 0; /* Start hidden */
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

        /* Icon shake-and-pulse animation */
        .shake-and-pulse {
            animation: shakeKeyframes 0.8s ease-out forwards, pulseRed 2s infinite ease-in-out 0.8s; /* Combined animations */
            opacity: 0; /* Ensure it starts hidden for the animation */
            transform: scale(0.9);
        }

        @keyframes shakeKeyframes {
            0% {
                opacity: 0;
                transform: translateX(0) scale(0.9);
            }
            20% { transform: translateX(-10px) scale(1); }
            40% { transform: translateX(10px) scale(1); }
            60% { transform: translateX(-7px) scale(1); }
            80% { transform: translateX(7px) scale(1); }
            100% {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes pulseRed {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* RTL specific styles */
        body.rtl {
            direction: rtl;
            font-family: 'Noto Sans Arabic', sans-serif;
        }
    </style>
</head>
<?php
    $currentLang = "ar"; // Changed to Arabic by default
    // Assuming you'll pass item_id and order_id to the payment failed page
    $itemId = request()->query('item_id', '2'); // Default to 2 for example
    $orderId = request()->query('order_id', '1'); // Default to 1 for example
?>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-red-50 to-pink-50 min-h-screen flex flex-col items-center justify-center @if($currentLang === 'ar') rtl @endif">

    <div class="max-w-xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto text-center">

        <div class="p-6 sm:p-8 bg-gradient-to-r from-red-600 to-pink-700 text-white rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="paymentFailedHeader">
                @if($currentLang === 'ar') فشل الدفع! @else Payment Failed! @endif
            </h1>
            <p class="text-red-200 text-lg" data-key="paymentErrorMessage">
                @if($currentLang === 'ar') للأسف، لم نتمكن من معالجة دفعك. @else Unfortunately, we couldn't process your payment. @endif
            </p>
        </div>

        <div class="p-6 sm:p-8 flex flex-col items-center justify-center">
            <div class="mb-6 shake-and-pulse">
                <svg class="w-24 h-24 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <p class="text-2xl font-semibold text-gray-800 mb-4 fade-in delay-100" data-key="somethingWentWrong">
                @if($currentLang === 'ar') حدث خطأ ما @else Something went wrong @endif
            </p>
            <p class="text-lg text-gray-600 mb-8 fade-in delay-200" data-key="pleaseTryAgain">
                @if($currentLang === 'ar') يرجى مراجعة تفاصيل الدفع والمحاولة مرة أخرى. @else Please check your payment details and try again. @endif
            </p>

            
            
        </div>

    </div>

    <script>
        let currentLang = @json($currentLang ?? 'en');
        const itemId = @json($itemId);
        const orderId = @json($orderId); // Order ID might not be relevant if payment failed before order creation

        const translations = {
            en: {
                pageTitle: "Payment Failed",
                paymentFailedHeader: "Payment Failed!",
                paymentErrorMessage: "Unfortunately, we couldn't process your payment.",
                somethingWentWrong: "Something went wrong",
                pleaseTryAgain: "Please check your payment details and try again.",
                tryAgainButton: "Try Again",
                continueShoppingLink: "Continue Shopping"
            },
            ar: {
                pageTitle: "فشل الدفع!",
                paymentFailedHeader: "فشل الدفع!",
                paymentErrorMessage: "للأسف، لم نتمكن من معالجة دفعك.",
                somethingWentWrong: "حدث خطأ ما",
                pleaseTryAgain: "يرجى مراجعة تفاصيل الدفع والمحاولة مرة أخرى.",
                tryAgainButton: "حاول مرة أخرى",
                continueShoppingLink: "متابعة التسوق"
            }
        };

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
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Set language to Arabic by default when the page loads
            updateContent('ar'); 
        });
    </script>

</body>
</html>
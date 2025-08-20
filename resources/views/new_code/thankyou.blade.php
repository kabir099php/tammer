<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">Thank You!</title>
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

        /* Icon bounce-in-and-pulse animation */
        .bounce-in-and-pulse {
            animation: bounceInKeyframes 1s ease-out forwards, pulse 2s infinite ease-in-out 1s; /* Combined animations */
            opacity: 0; /* Ensure it starts hidden for the animation */
            transform: scale(0.5);
        }

        @keyframes bounceInKeyframes {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            70% {
                opacity: 1; /* Fully visible during bounce */
                transform: scale(1.1);
            }
            100% {
                opacity: 1; /* Keep visible after bounce */
                transform: scale(1);
            }
        }

        @keyframes pulse {
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
    $currentLang = "ar"; // Or whatever your default is or passed from controller
    // Assuming you'll pass item_id and order_id to the thank you page
    $itemId = request()->query('item_id', '2'); // Default to 2 for example
    $orderId = request()->query('order_id', '1'); // Default to 1 for example
?>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-[#E0F7FA] to-[#E8F5E9] min-h-screen flex flex-col items-center justify-center @if($currentLang === 'ar') rtl @endif">

    <div class="max-w-xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto text-center">

        <div class="p-6 sm:p-8 bg-gradient-to-r from-[#00AD78] to-[#00261B] text-white rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="thankYouHeader">
                @if($currentLang === 'ar') شكراً لك! @else Thank You! @endif
            </h1>
            <p class="text-[#00E6A0] text-lg" data-key="orderSuccessMessage">
                @if($currentLang === 'ar') تم تأكيد طلبك بنجاح. @else Your order has been successfully confirmed. @endif
            </p>
        </div>

        <div class="p-6 sm:p-8 flex flex-col items-center justify-center">
            <div class="mb-6 bounce-in-and-pulse">
                <svg class="w-24 h-24 text-[#00AD78] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <p class="text-2xl font-semibold text-gray-800 mb-4 fade-in delay-100" data-key="paymentProcessed">
                @if($currentLang === 'ar') تم معالجة الدفع بنجاح @else Payment processed successfully @endif
            </p>
            <p class="text-lg text-gray-600 mb-8 fade-in delay-200" data-key="orderCreated">
                @if($currentLang === 'ar') وتم إنشاء طلبك. @else and your order has been created. @endif
            </p>
        <a href="#" id="downloadInvoiceButton" class="w-full sm:w-auto px-8 py-4 bg-[#00AD78] text-white font-bold rounded-lg shadow-lg hover:bg-[#00261B] focus:outline-none focus:ring-2 focus:ring-[#00E6A0] focus:ring-opacity-75 transition duration-300 ease-in-out transform hover:scale-105 fade-in delay-300" data-key="viewReceiptButton">
                @if($currentLang === 'ar') عرض الإيصال @else View Receipt @endif
            </a>
            <a href="/checkout?store_id={{$order->store_id}}" class="mt-4 text-[#00AD78] hover:underline fade-in delay-400" data-key="continueShoppingLink">
                @if($currentLang === 'ar') متابعة التسوق @else Continue Shopping @endif
            </a>
        </div>

    </div>

    <script>
        let currentLang = @json($currentLang ?? 'en');
        const itemId = @json($itemId);
        const orderId = @json($orderId);

        const translations = {
            en: {
                pageTitle: "Thank You!",
                thankYouHeader: "Thank You!",
                orderSuccessMessage: "Your order has been successfully confirmed.",
                paymentProcessed: "Payment processed successfully",
                orderCreated: "and your order has been created.",
                viewReceiptButton: "View Receipt",
                continueShoppingLink: "Continue Shopping"
            },
            ar: {
                pageTitle: "شكراً لك!",
                thankYouHeader: "شكراً لك!",
                orderSuccessMessage: "تم تأكيد طلبك بنجاح.",
                paymentProcessed: "تم معالجة الدفع بنجاح",
                orderCreated: "وتم إنشاء طلبك.",
                viewReceiptButton: "عرض الإيصال",
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

        // Function to handle PDF download via AJAX
        function downloadPdfInvoice(orderId) {
            const invoiceUrl = `https://waslqr.com/invoice/${orderId}`; // Construct the URL

            fetch(invoiceUrl, {
                method: 'GET',
                headers: {
                    // If your Laravel backend requires CSRF token for GET requests, include it here.
                    // For a simple GET to download, it's often not needed.
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => {
                if (!response.ok) {
                    // If the server response is not OK (e.g., 404, 500), throw an error.
                    // It's good practice to try to read the error message if available.
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                    });
                }
                return response.blob(); // Get the response body as a Blob (binary data)
            })
            .then(blob => {
                // Create a URL for the Blob
                const url = window.URL.createObjectURL(blob);

                // Create a temporary anchor element
                const a = document.createElement('a');
                a.style.display = 'none'; // Hide the link
                a.href = url;
                a.download = `invoice-${orderId}.pdf`; // Set the desired filename

                // Append the link to the body and programmatically click it
                document.body.appendChild(a);
                a.click();

                // Clean up: revoke the object URL and remove the link
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            })
            .catch(error => {
                console.error('Error downloading invoice:', error);
                alert(`Failed to download invoice. Please try again. Error: ${error.message}`);
                // You might want to display a more user-friendly error message here
            });
        }


        document.addEventListener('DOMContentLoaded', () => {
            if (currentLang === 'ar') {
                document.body.classList.add('rtl');
                document.documentElement.setAttribute('dir', 'rtl');
            } else {
                document.body.classList.remove('rtl');
                document.documentElement.removeAttribute('dir');
            }
            updateContent(currentLang);

            // Get the "View Receipt" button
            const downloadButton = document.getElementById('downloadInvoiceButton');

            // Add a click event listener to the button
            if (downloadButton) {
                downloadButton.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent the default link behavior (e.g., navigating to '#')
                    // Call the download function with the orderId
                    downloadPdfInvoice(orderId);
                });
            }

            // If you want to auto-download the PDF immediately when the page loads,
            // uncomment the line below. Be cautious with auto-downloads as they can be blocked by browsers.
            // downloadPdfInvoice(orderId); // Uncomment to auto-download on page load
        });
    </script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-key="pageTitle">Give Feedback</title>
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

        /* Star rating animation - REVISED FOR CORRECT SELECTION BEHAVIOR */
        .star-container {
            display: flex;
            justify-content: center;
            flex-direction: row-reverse; /* Display stars from right to left (5 to 1) */
        }

        .star-container input {
            display: none; /* Hide the radio buttons */
        }

        .star-container label {
            cursor: pointer;
            font-size: 3rem; /* Large stars */
            color: #ccc; /* Default grey color */
            transition: color 0.2s ease-in-out, transform 0.2s ease-in-out;
            padding: 0 0.2rem; /* Add some padding between stars */
        }

        /* When an input is checked, color it and all subsequent labels (to its right in HTML, left visually) */
        .star-container input:checked ~ label {
            color: #FBBF24; /* Amber-400 for selected stars */
        }

        /* On hover, color the hovered star and all previous labels (to its left in HTML, right visually) */
        .star-container label:hover,
        .star-container label:hover ~ label {
            color: #FBBF24; /* Amber-400 for hovered stars */
            transform: scale(1.1); /* Slight bounce on hover */
        }

        /* Reset transform when not hovered for a smoother transition */
        .star-container label:not(:hover) {
            transform: scale(1);
        }

        /* Ensure the checked star remains scaled slightly */
        .star-container input:checked + label {
            transform: scale(1.1);
        }
         /* And stars that were previously selected and are now part of the "unselected" group should revert */
        .star-container input:not(:checked) ~ label {
             transform: scale(1);
        }

        /* RTL specific styles for text alignment */
        body.rtl .text-right-on-rtl {
            text-align: right;
        }
        body.rtl input::placeholder,
        body.rtl textarea::placeholder {
            text-align: right;
        }
        body.rtl input,
        body.rtl textarea {
            text-align: right;
        }

        /* Ensure .star-container handles RTL correctly without breaking selection */
        body.rtl .star-container {
            direction: ltr; /* Important: Star logic relies on LTR internal order, reversed by flex-direction */
            flex-direction: row-reverse; /* Visually arrange 5-4-3-2-1 for RTL */
        }
        /* Specific rules for RTL hover/checked effects if general ones fail */
        body.rtl .star-container label:hover,
        body.rtl .star-container label:hover ~ label {
            color: #FBBF24;
        }
        body.rtl .star-container input:checked ~ label {
            color: #FBBF24;
        }
    </style>
</head>
<?php
    $currentLang = "ar"; // Or whatever your default is or passed from controller
    $itemId = request()->query('item_id', '2'); // Get from URL or default
    $orderId = request()->query('order_id', '1'); // Get from URL or default
?>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex flex-col items-center justify-center @if($currentLang === 'ar') rtl @endif">

    <div class="max-w-xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 hover:scale-[1.005] mx-auto text-center">

        <div class="p-6 sm:p-8 bg-gradient-to-r from-purple-600 to-indigo-700 text-white rounded-t-xl fade-in">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" data-key="feedbackHeader">
                @if($currentLang === 'ar') شاركنا رأيك @else Share Your Feedback @endif
            </h1>
            <p class="text-indigo-200 text-lg" data-key="feedbackMessage">
                @if($currentLang === 'ar') نود أن نسمع منك! @else We'd love to hear from you! @endif
            </p>
        </div>

        <div class="p-6 sm:p-8 flex flex-col items-center justify-center">

            <div class="mb-6 w-full fade-in delay-100">
                <p class="text-xl font-semibold text-gray-800 mb-4" data-key="howWasYourExperience">
                    @if($currentLang === 'ar') كيف كانت تجربتك؟ @else How was your experience? @endif
                </p>
                <div class="star-container">
                    {{-- Stars are rendered from 5 to 1 in HTML to make the CSS `~` selector work correctly for visual filling --}}
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                        <label for="star{{ $i }}">★</label>
                    @endfor
                </div>
            </div>

            <div class="w-full mb-6 fade-in delay-200">
                <label for="comment" class="block text-lg font-medium text-gray-700 mb-2 text-right-on-rtl" data-key="yourComments">
                    @if($currentLang === 'ar') تعليقاتك (اختياري) @else Your Comments (Optional) @endif
                </label>
                <textarea id="comment" rows="5" placeholder="@if($currentLang === 'ar') اكتب تعليقاتك هنا... @else Type your comments here... @endif" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
            </div>

            <button id="submit-feedback-button" class="w-full px-8 py-4 bg-green-500 text-white font-bold rounded-lg shadow-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75 transition duration-300 ease-in-out transform hover:scale-105 fade-in delay-300">
                <span data-key="submitFeedback">
                    @if($currentLang === 'ar') إرسال التقييم @else Submit Feedback @endif
                </span>
            </button>
        </div>

    </div>

    <script>
        let currentLang = @json($currentLang ?? 'en');
        const itemId = @json($itemId);
        const orderId = @json($orderId);

        const translations = {
            en: {
                pageTitle: "Give Feedback",
                feedbackHeader: "Share Your Feedback",
                feedbackMessage: "We'd love to hear from you!",
                howWasYourExperience: "How was your experience?",
                yourComments: "Your Comments (Optional)",
                submitFeedback: "Submit Feedback",
                selectRating: "Please select a star rating before submitting.",
                feedbackSuccess: "Thank you for your feedback!",
                feedbackError: "Failed to submit feedback. Please try again later."
            },
            ar: {
                pageTitle: "أرسل رأيك",
                feedbackHeader: "شاركنا رأيك",
                feedbackMessage: "نود أن نسمع منك!",
                howWasYourExperience: "كيف كانت تجربتك؟",
                yourComments: "تعليقاتك (اختياري)",
                submitFeedback: "إرسال التقييم",
                selectRating: "الرجاء تحديد تقييم بالنجوم قبل الإرسال.",
                feedbackSuccess: "شكرا لك على ملاحظاتك!",
                feedbackError: "فشل إرسال التعليقات. الرجاء المحاولة مرة أخرى لاحقًا."
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
            if (currentLang === 'ar') {
                document.body.classList.add('rtl');
                document.documentElement.setAttribute('dir', 'rtl');
            } else {
                document.body.classList.remove('rtl');
                document.documentElement.removeAttribute('dir');
            }
            updateContent(currentLang);

            const submitButton = document.getElementById('submit-feedback-button');
            const starInputs = document.querySelectorAll('.star-container input[name="rating"]');
            let selectedRating = 0;

            // This JS part ensures `selectedRating` is updated correctly
            starInputs.forEach(input => {
                input.addEventListener('change', function() {
                    selectedRating = parseInt(this.value);
                    console.log("Selected rating:", selectedRating);
                });
            });

            submitButton.addEventListener('click', async () => {
                if (selectedRating === 0) {
                    alert(translations[currentLang].selectRating);
                    return;
                }

                const comment = document.getElementById('comment').value;

                // API call details
                const apiUrl = 'https://trymajlis.com/api/v1/items/reviews/submit';
                // IMPORTANT: In a real application, you would dynamically get the XSRF-TOKEN.
                // For Laravel, ensure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your <head>
                const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

                const formData = new FormData();
                formData.append('item_id', itemId);
                formData.append('order_id', orderId);
                formData.append('rating', selectedRating);
                formData.append('comment', comment);

                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken, // Use the dynamically retrieved token
                            // 'Cookie': cookieHeader // Browser usually handles this automatically for same-origin
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const data = await response.json();
                        alert(translations[currentLang].feedbackSuccess);
                        console.log("Feedback API response:", data);
                        // Optionally redirect or clear form after successful submission
                        // window.location.href = '/some-confirmation-page';
                    } else {
                        const errorData = await response.json();
                        alert(`${translations[currentLang].feedbackError}: ${errorData.message || 'Unknown error'}`);
                        console.error('Feedback API error:', errorData);
                    }
                } catch (error) {
                    console.error('Network error or unexpected:', error);
                    alert("An error occurred. Please check your network connection.");
                }
            });
        });
    </script>

</body>
</html>
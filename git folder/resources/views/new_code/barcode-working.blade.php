<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Noto Sans Arabic Font for RTL support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <!-- QuaggaJS CDN for barcode scanning -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <style>
        /* Custom styles for animations and font from the provided HTML */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray fallback background */
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
                flex-direction: row; /* Side-by-side columns, although scanner is single column */
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

        /* Specific styles for barcode scanner viewport */
        #interactive.viewport {
            position: relative;
            width: 100%;
            height: auto;
            max-width: 600px; /* Consistent max width */
            margin: 0 auto;
            overflow: hidden;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background-color: #000;
        }
        #interactive.viewport canvas,
        #interactive.viewport video {
            display: block;
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 0.75rem;
        }
        .drawingBuffer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Custom styles for the message box, integrated with the new design */
        .message-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .message-box.show {
            opacity: 1;
            visibility: visible;
        }
        .message-box-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .message-box-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body class="p-4 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-50 to-purple-50">

    <div class="main-container w-full bg-white rounded-xl shadow-2xl overflow-hidden md:flex transform transition-all duration-300 hover:scale-[1.005]">

        <!-- Language buttons - hidden as core scanner strings are not translated, can be shown if needed -->
        <div class="absolute top-4 right-4 z-10 flex space-x-2 fade-in delay-0" style="display:none">
            <button id="lang-en" class="px-4 py-2 bg-purple-500 text-white rounded-lg shadow-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75 transition duration-200">
                EN
            </button>
            <button id="lang-ar" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200">
                AR
            </button>
        </div>

        <div class="p-6 sm:p-8 flex flex-col w-full justify-center items-center bg-white rounded-b-xl md:rounded-xl">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-center text-gray-800 mb-5 heading-rtl-align fade-in delay-100">Barcode Scanner</h1>

            <div id="interactive" class="viewport mb-5 fade-in delay-200">
                <!-- The video stream will be inserted here -->
            </div>

            <div class="bg-gray-50 p-4 rounded-lg shadow-inner mb-5 w-full max-w-lg text-center fade-in delay-300">
                <h2 class="text-lg font-semibold text-gray-700 mb-2 heading-rtl-align">Scanned Result:</h2>
                <p id="result" class="text-gray-900 text-xl font-mono break-words">- No barcode scanned yet -</p>
            </div>

            <button id="sendDataButton" class="w-full sm:w-auto px-8 py-4 bg-purple-600 text-white font-bold rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-75 transition duration-150 ease-in-out transform hover:scale-105 fade-in delay-400" disabled>
                Add To Cart 
            </button>
        </div>

        <!-- Message Box for alerts -->
        <div id="messageBoxOverlay" class="message-box-overlay"></div>
        <div id="messageBox" class="message-box">
            <p id="messageBoxText" class="text-lg font-medium text-gray-800 mb-4"></p>
            <button id="messageBoxClose" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Close</button>
        </div>

    </div>

    <script>
        let QuaggaInitialized = false;
        const resultElement = document.getElementById('result');
        const sendDataButton = document.getElementById('sendDataButton');
        const messageBox = document.getElementById('messageBox');
        const messageBoxText = document.getElementById('messageBoxText');
        const messageBoxClose = document.getElementById('messageBoxClose');
        const messageBoxOverlay = document.getElementById('messageBoxOverlay');

        // Function to show a custom message box
        function showMessageBox(message) {
            messageBoxText.textContent = message;
            messageBox.classList.add('show');
            messageBoxOverlay.classList.add('show');
        }

        // Function to hide the custom message box
        function hideMessageBox() {
            messageBox.classList.remove('show');
            messageBoxOverlay.classList.remove('show');
        }

        // Event listener for message box close button
        messageBoxClose.addEventListener('click', hideMessageBox);
        messageBoxOverlay.addEventListener('click', hideMessageBox); // Close on overlay click too

        // Function to set language and apply RTL if needed (basic implementation)
        function setLanguageDirection(lang) {
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

        /**
         * Initializes and starts the barcode scanner.
         */
        function startScanner() {
            const config = {
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'),
                    constraints: {
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "codabar_reader",
                        "upc_reader",
                        "upc_e_reader",
                        "i2of5_reader",
                        "2of5_reader",
                        "code_93_reader"
                    ]
                },
                locate: true,
                frequency: 10,
                debug: {
                    drawBoundingBox: true,
                    drawScanline: true,
                    showFrequency: true,
                    showPattern: true,
                }
            };

            Quagga.init(config, function(err) {
                if (err) {
                    console.error("Error initializing Quagga:", err);
                    resultElement.textContent = `Error: ${err.message || err}`;
                    showMessageBox(`Failed to start camera: ${err.message}. Please ensure camera access is granted.`);
                    return;
                }
                Quagga.start();
                QuaggaInitialized = true;
                sendDataButton.disabled = true; // Disable until a barcode is scanned
                resultElement.textContent = "Scanning...";
                console.log("QuaggaJS started successfully.");
            });

            Quagga.onDetected(function(result) {
                if (result && result.codeResult) {
                    const code = result.codeResult.code;
                    console.log("Barcode detected:", code);
                    resultElement.textContent = `Detected: ${code}`;
                    sendDataButton.disabled = false;
                    // Optional: Quagga.stop(); // Uncomment to stop scanning after first successful read
                }
            });

            Quagga.onProcessed(function(result) {
                const drawingCtx = Quagga.canvas.ctx.overlay;
                const drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.width), parseInt(drawingCanvas.height));
                        result.boxes.filter(function(box) {
                            return box !== result.box;
                        }).forEach(function(box) {
                            Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                        });
                    }
                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
                    }
                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: "red", lineWidth: 3 });
                    }
                }
            });
        }

        /**
         * Sends the scanned data to a mock endpoint using AJAX (fetch API).
         */
        async function sendData() {
            const barcodeData = resultElement.textContent.replace('Detected: ', '');
            if (barcodeData === "- No barcode scanned yet -" || barcodeData === "Scanning...") {
                showMessageBox("Please scan a barcode first before sending data.");
                return;
            }

            console.log("Attempting to send data:", barcodeData);
            showMessageBox("Sending data...");

            try {
                const response = await fetch('https://jsonplaceholder.typicode.com/posts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ barcode: barcodeData, timestamp: new Date().toISOString() }),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Data sent successfully:', data);
                showMessageBox(`Item Added to cart`);
            } catch (error) {
                console.error('Error sending data:', error);
                showMessageBox(`Failed to send data: ${error.message}. Check console for details.`);
            }
        }

        // Event listener for send data button
        sendDataButton.addEventListener('click', sendData);

        // Initial setup: Start scanner automatically and set default language (EN)
        document.addEventListener('DOMContentLoaded', () => {
            setLanguageDirection('en'); // Default to English (LTR)
            startScanner();
        });

        // Language button event listeners (currently hidden in HTML but kept for logic)
        document.getElementById('lang-en').addEventListener('click', () => setLanguageDirection('en'));
        document.getElementById('lang-ar').addEventListener('click', () => setLanguageDirection('ar'));
    </script>
</body>
</html>

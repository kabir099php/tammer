<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font (still included but less critical for a full-screen scanner) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Noto Sans Arabic Font for RTL support (still included but less critical) -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <!-- QuaggaJS CDN for barcode scanning -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #000; /* Black background for a seamless look with the scanner */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden; /* Hide scrollbars */
        }

        /* Full screen interactive viewport for the scanner */
        #interactive.viewport {
            position: fixed; /* Fixed position to cover entire viewport */
            top: 0;
            left: 0;
            width: 100vw; /* Full viewport width */
            height: 100vh; /* Full viewport height */
            overflow: hidden;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10; /* Lower z-index than logo and message box */
        }

        #interactive.viewport canvas,
        #interactive.viewport video {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Cover the entire area, cropping if aspect ratios don't match */
        }

        .drawingBuffer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Custom styles for the logo */
        .logo-container {
            position: fixed;
            top: 1rem; /* Adjust distance from top */
            left: 50%;
            transform: translateX(-50%);
            z-index: 100; /* Higher z-index than scanner, lower than message box */
            /* Removed background-color and box-shadow to make it transparent */
            padding: 0.5rem 1rem; /* Keep padding for spacing around the image */
            border-radius: 0.5rem;
        }

        .logo-container img {
            height: 70px; /* Adjust logo height */
            width: auto;
            /* Optional: Add a subtle shadow to the image itself if needed */
            /* filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5)); */
        }

        /* Custom styles for the cart button and badge */
        .cart-button-container {
            position: fixed;
            top: 1rem; /* Adjust distance from top */
            right: 1rem; /* Adjust distance from right */
            z-index: 100; /* Higher z-index than scanner, lower than message box */
        }

        .cart-button {
            background-color: rgba(255, 255, 255, 0.8); /* Slightly translucent white */
            color: #333;
            padding: 0.75rem;
            border-radius: 9999px; /* Fully rounded */
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.2s ease;
        }

        .cart-button:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        .cart-icon {
            height: 24px;
            width: 24px;
            fill: currentColor; /* Use current text color for icon */
        }

        .cart-badge {
            position: absolute;
            top: -0.5rem; /* Adjust badge position */
            right: -0.5rem; /* Adjust badge position */
            background-color: #ef4444; /* Red color */
            color: white;
            font-size: 0.75rem; /* text-xs */
            font-weight: bold;
            border-radius: 9999px; /* Fully rounded */
            padding: 0.25rem 0.5rem;
            min-width: 1.5rem; /* Ensure minimum size */
            text-align: center;
            line-height: 1; /* Adjust line height for vertical centering */
            border: 2px solid rgba(255, 255, 255, 0.8); /* White border for contrast */
        }


        /* Custom styles for the message box, visible over the full-screen scanner */
        .message-box {
            position: fixed;
            bottom: 0; /* Stick to the bottom */
            left: 0;
            width: 100%; /* Full width */
            transform: translateX(0); /* No horizontal transform needed for full width */
            background-color: rgba(255, 255, 255, 0.95); /* Slightly translucent white */
            padding: 2rem;
            border-top-left-radius: 0.75rem; /* rounded-xl only at top */
            border-top-right-radius: 0.75rem; /* rounded-xl only at top */
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            box-shadow: 0 -10px 15px -3px rgba(0, 0, 0, 0.2), 0 -4px 6px -2px rgba(0, 0, 0, 0.1); /* Shadow cast upwards */
            z-index: 1000; /* Ensure it's above the scanner and logo */
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease; /* Add transform transition */
            color: #333; /* Darker text for readability */
            max-width: none; /* Override max-width for full width */
        }
        .message-box.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0); /* Ensure it slides up or just appears */
        }
        /* Initial hidden state for slide-up effect */
        .message-box:not(.show) {
            transform: translateY(100%); /* Start off-screen at the bottom */
        }

        .message-box-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Darker overlay */
            z-index: 999; /* Ensure it's above the scanner and logo */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .message-box-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* RTL specific styles (less relevant for a purely visual scanner, but kept for consistency) */
        body.rtl {
            direction: rtl;
            font-family: 'Noto Sans Arabic', sans-serif;
        }
    </style>
</head>
<body>

    <!-- Sample Logo on top -->
    <div class="logo-container">
        <img src="https://waslqr.com/storage/app/public/business/logo2.png" alt="Company Logo">
    </div>

    <!-- Cart Button with Badge -->
    <div class="cart-button-container">
        <button id="cart-button" class="cart-button relative">
            <!-- Cart Icon (Inline SVG for simplicity) -->
            <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span id="cart-count-badge" class="cart-badge">0</span>
        </button>
    </div>

    <!-- Full-screen interactive viewport for the barcode scanner -->
    <div id="interactive" class="viewport">
        <!-- The video stream will be inserted here by QuaggaJS -->
    </div>

    <!-- Message Box for alerts/feedback -->
    <div id="messageBoxOverlay" class="message-box-overlay"></div>
    <div id="messageBox" class="message-box">
        <p id="messageBoxText" class="text-lg font-medium mb-4"></p>
        <button id="messageBoxClose" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Close</button>
    </div>

    <script>
        let QuaggaInitialized = false;
        let lastSentBarcode = null; // To prevent duplicate API calls for the same barcode
        let isSendingData = false; // Flag to prevent multiple concurrent AJAX calls
        let cartItemCount = 0; // Initialize cart count

        // Elements for the message box
        const messageBox = document.getElementById('messageBox');
        const messageBoxText = document.getElementById('messageBoxText'); // This now references the <p> tag inside the messageBox
        const messageBoxClose = document.getElementById('messageBoxClose');
        const messageBoxOverlay = document.getElementById('messageBoxOverlay');
        const cartCountBadge = document.getElementById('cart-count-badge'); // Get the cart badge element
        const cartButton = document.getElementById('cart-button'); // Get the cart button element

        // Function to update the cart badge count
        function updateCartBadge() {
            cartCountBadge.textContent = cartItemCount;
        }

        // Function to show a custom message box
        function showMessageBox(message) {
            messageBoxText.innerHTML = message; // Changed to innerHTML to render HTML content
            messageBox.classList.add('show');
            messageBoxOverlay.classList.add('show');
            // Pause the scanner when the message box is shown
            if (QuaggaInitialized) {
                Quagga.stop();
                console.log("QuaggaJS paused.");
                // Crucial: Set QuaggaInitialized to false to force re-initialization on close
                QuaggaInitialized = false;
            }
        }

        // Function to hide the custom message box
        function hideMessageBox() {
            messageBox.classList.remove('show');
            messageBoxOverlay.classList.remove('show');
            // Always call startScanner to resume/re-initialize the camera
            startScanner();
        }

        // Event listener for message box close button
        messageBoxClose.addEventListener('click', hideMessageBox);
        messageBoxOverlay.addEventListener('click', hideMessageBox); // Close on overlay click too

        // Event listener for cart button
        cartButton.addEventListener('click', () => {
            // Pause scanner before redirecting
            if (QuaggaInitialized) {
                Quagga.stop();
                console.log("QuaggaJS stopped for redirection.");
            }
            // Redirect to the Laravel checkout page
            window.location.href = '/scanner-checkout'; // This should be your Laravel checkout route
        });


        /**
         * Initializes and starts the barcode scanner.
         * This function now explicitly re-initializes Quagga every time it's called
         * after a stop, ensuring the camera stream comes back.
         */
        function startScanner() {
            const config = {
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'), // Target element for the video stream
                    constraints: {
                        facingMode: "environment" // Use the rear camera
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

            // Re-initialize Quagga every time this is called, especially after a stop.
            // Quagga.init will handle cleaning up previous streams if any.
            Quagga.init(config, function(err) {
                if (err) {
                    console.error("Error initializing Quagga:", err);
                    showMessageBox(`Failed to start camera: ${err.message}. Please ensure camera access is granted.`);
                    // Important: Do not set QuaggaInitialized to true if initialization fails
                    return;
                }
                Quagga.start();
                QuaggaInitialized = true; // Set to true only on successful init and start
                console.log("QuaggaJS initialized and started successfully, full-screen.");
            });


            // Listen for barcode detection events
            Quagga.onDetected(function(result) {
                // Only process if scanner is "active" (not paused by message box)
                if (messageBox.classList.contains('show')) {
                    console.log("Scanner paused, ignoring new barcode detection.");
                    return;
                }

                if (result && result.codeResult && result.codeResult.code !== lastSentBarcode) {
                    const code = result.codeResult.code;
                    console.log("Barcode detected:", code);
                    // Display result via message box and send data
                    // showMessageBox(`Barcode Detected: ${code}\nSending data...`); // This will be replaced by sendData's message
                    sendData(code); // Automatically send data
                }
            });

            // Listen for drawing events to visualize the detection
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
         * @param {string} barcodeData The barcode string to send.
         */
        async function sendData(barcodeData) {
            if (isSendingData) {
                console.warn("Already sending data, skipping duplicate call.");
                return;
            }

            isSendingData = true;
            showMessageBox(`
                <div class="space-y-2">
                    <p class="text-xl font-bold">Processing Barcode:</p>
                    <p class="text-gray-600">${barcodeData}</p>
                    <p class="mt-4 animate-pulse text-purple-600">Sending data...</p>
                </div>
            `); // Update message box for sending status
            console.log("Attempting to send data:", barcodeData);

            try {
                // Replace with your actual Laravel API endpoint
                const response = await fetch('https://trymajlis.com/scan-barcode', { // Using relative path for API endpoint
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ barcode: barcodeData, timestamp: new Date().toISOString() }),
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: 'No error message available.' }));
                    throw new Error(`HTTP error! status: ${response.status}. Message: ${errorData.message || 'Unknown error'}`);
                }

                const data = await response.json();
                console.log('Data sent successfully:', data);
                showMessageBox(`
                    <div class="space-y-2">
                        <p class="text-xl font-bold text-green-600">Item Added!</p>
                        <p><span class="font-semibold">Name:</span> ${data.item.name}</p>
                        <p><span class="font-semibold">Price:</span> ${data.item.price.toFixed(2)} SAR/kg</p>
                        <p><span class="font-semibold text-purple-700 text-lg">Cart Total:</span> ${data.cart_total_price.toFixed(2)} SAR</p>
                    </div>
                `);
                lastSentBarcode = data.item.barcode; // Ensure lastSentBarcode is updated with the actually processed barcode

                // Update cart count and update badge upon successful send
                cartItemCount = data.total_items_in_cart; // Use the total quantity from the Laravel response
                updateCartBadge();

            } catch (error) {
                console.error('Error sending data:', error);
                showMessageBox(`
                    <div class="space-y-2">
                        <p class="text-xl font-bold text-red-600">Error!</p>
                        <p class="text-gray-700">${error.message}.</p>
                        <p class="text-sm text-gray-500">Check console for details.</p>
                    </div>
                `);
            } finally {
                isSendingData = false;
            }
        }

        // Initial setup: Start scanner automatically on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadge(); // Initialize badge count to 0
            startScanner();
        });
    </script>
</body>
</html>

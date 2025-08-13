<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scandit Barcode Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            /* No longer a flex container for central alignment as elements are fixed */
            margin: 0;
            padding: 0; /* Remove body padding to allow full screen */
            overflow: hidden; /* Prevent scrolling if content overflows */
        }
        #scandit-barcode-picker {
            position: fixed; /* Make it cover the entire viewport */
            top: 0;
            left: 0;
            width: 100vw; /* Full viewport width */
            height: 100vh; /* Full viewport height */
            background-color: #000;
            border-radius: 0; /* No rounded corners for true full screen */
            overflow: hidden;
            box-shadow: none; /* No shadow for full screen */
            z-index: 10; /* Behind other overlay elements */
        }
        h1 {
            position: fixed; /* Overlay on top of the scanner */
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff; /* White text for contrast */
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5); /* Add shadow for readability */
            margin: 0; /* Remove default margin */
            font-size: 2.25rem; /* text-4xl */
            font-weight: 700; /* font-bold */
            z-index: 20; /* On top of the scanner */
            text-align: center;
            width: 90%; /* Ensure it fits on smaller screens */
            max-width: 600px;
        }
        #results {
            position: fixed; /* Overlay on top of the scanner */
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%; /* Responsive width */
            max-width: 500px; /* Max width for larger screens */
            z-index: 20; /* On top of the scanner */
            backdrop-filter: blur(5px); /* Optional: adds a blur effect behind the box */
        }
        #scannedBarcode {
            font-weight: bold;
            color: #2563eb; /* blue-600 */
        }
        .scandit-disclaimer {
            position: fixed; /* Overlay on top of the scanner */
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.875rem; /* text-sm */
            color: #ffffff; /* White text for contrast */
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5); /* Add shadow for readability */
            z-index: 20; /* On top of the scanner */
            text-align: center;
            width: 90%;
            max-width: 600px;
        }
        .scandit-disclaimer a {
            color: #a5f3fc; /* light blue for links */
            text-decoration: none;
            font-weight: 600;
        }
        .scandit-disclaimer a:hover {
            text-decoration: underline;
        }
        /* Message box for alerts */
        .message-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            min-width: 280px;
            max-width: 90%;
        }
        .message-box-content {
            margin-bottom: 20px;
            font-size: 1.125rem;
            color: #333;
        }
        .message-box-button {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s ease;
        }
        .message-box-button:hover {
            background-color: #1d4ed8;
        }
        .camera-warning {
            position: fixed; /* Overlay on top of the scanner */
            top: 80px; /* Adjusted to be below the H1 */
            left: 50%;
            transform: translateX(-50%);
            color: #dc2626; /* red-600 */
            background-color: rgba(254, 226, 226, 0.9); /* Semi-transparent red background */
            border: 1px solid #ef4444; /* red-500 */
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            display: none; /* Hidden by default, shown by JS if needed */
            z-index: 20; /* On top of the scanner */
            width: 90%;
            max-width: 600px;
            backdrop-filter: blur(5px); /* Optional: adds a blur effect behind the box */
        }
    </style>
</head>
<body>
    <h1 class="text-4xl font-bold mb-6">Scandit Barcode Scanner</h1>

    <div id="camera-access-warning" class="camera-warning">
        <p><strong>Camera Access Required:</strong> Your browser might not fully support live camera scanning. Please ensure you are using a modern browser on a secure (HTTPS) connection and have granted camera permissions.</p>
    </div>

    <div id="scandit-barcode-picker"></div>

    <div id="results">
        <p class="text-lg text-gray-700">Scanned Barcode: <span id="scannedBarcode" class="font-semibold text-blue-600">None</span></p>
    </div>

    <div class="scandit-disclaimer">
        Powered by <a href="https://www.scandit.com" target="_blank">Scandit</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x"></script>

    <script>
        // IMPORTANT: Replace with your actual Scandit License Key.
        // This key is for demonstration purposes and might have limitations.
        const SCANDIT_LICENSE_KEY = 'Al2mxzqeHAiwJ3BYCuiLsUoYipFtFY1M+ScJogYV1i3Kbhht+iOpmIJQT0ZhSTZ02k60iwFvx3csGjvxRzh11sVypxxTD38QdVMunsU/51VrK3s5RyK3uskP+HJoQEpA115VolBNDMUbWpKDRm0B/aMjo1ZebjtjDUxSghhS2hd2dIAUz0bwkQ9oC4uFYYTB6nOlrOB9yfAWSpxrYmN7tkdXdnvsbN+8e05in/RvFt2eDyRD02SK5iF227QJcPJH1AwWmwRZXFSseXdgFVjSJX1uoob3Vo9L5WEvNsRe6uOFQTZj8HsJAt1A9qgoSteECCDJ1bhqHmIXaPstXFLZBd9tySSgITbs6G54ANdyUGyzUzgtCnkSz+9ENZ+TWo3bVWzUxOtB1gDYe00GoULPdCZ2HpW0ZVnnTD7BbqFk2p3+U2mbR05BvA1dyYxyXuDYxkrw4CJzCWvfY816HlhjYN9SK8N8OJFm/n0bmFVSEwz0U7laYW05BV0vYRG8cWYpY1S4I1JhzedGUzEGmjSnZ1MQvqrH1fOxVd2SQn2nxk5r5IEmqwWpylhp8RjXSv2ljvhcJJ4f4HaeKkja4Cur9IHOPjKZVH3SraEFRyWQD+LeTvmBQj5s6Ff9JKQ202JFmDUQ60gEn1ZYluUVl30nvj/a4JdKHuWSdKQ6674+FYNfgfcnC+rd+DAre8h/z5Vg8h9TtrqQzmpM7EnyO6sIK/SYkHeei3ZbgUAAQlB8YJ1TshfFjMBQYH5k8vOe74XqY1LqL/MlRA5ri0caJqVeTtQFo7yWmvOTg+qC956kvEFjwF1+Y78l0kUQLoLDsf0UX0FehuSQ4B7eerAxe4p5f2n3F/20Px8ulOHXCro/lQe+GYf9bLeks6qzkxiYCMy2bRtQyY0j+gfY92YqxiotHhRpiWy0a49iyVvahiGC2ewTgkp/LUPXry+CmcYl5NT5KpZmKDhG0/soZaElAVr27RbL+gAeXO3csjqINB1McwRgn9XM7qo00eAN4fbU4ME3UlzTCO7P6ngAWFBtXHDptrRyt9vz7mp+Y1z9FEPcMhEs5HUjX3f6KsECNKGSTuZx3mHTtCPV/To9hu0I+Nl8TMpDNrFGXRZeQMo1Dz3Zadx8tZinwmUP8Izv2yRRH3FTAcrMd2LR4Sja1dWPV42bTCYqpxC7hlrlx/qzoZL0IbYG23Io2O3lDn5pHWNsd91TuY0=';

        // Function to display a custom message box instead of alert()
        function showMessageBox(message) {
            const messageBox = document.createElement('div');
            messageBox.className = 'message-box';
            messageBox.innerHTML = `
                <div class="message-box-content">${message}</div>
                <button class="message-box-button" onclick="this.parentNode.remove()">OK</button>
            `;
            document.body.appendChild(messageBox);
        }

        // Initialize Scandit SDK and BarcodePicker when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', async () => {
            const cameraWarningElement = document.getElementById('camera-access-warning');

            // Check for MediaDevices API availability early
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                console.log("navigator.mediaDevices and getUserMedia are available.");
                cameraWarningElement.style.display = 'none'; // Hide warning if APIs are available
            } else {
                console.warn("navigator.mediaDevices or getUserMedia are NOT available. Camera access might be problematic.");
                cameraWarningElement.style.display = 'block'; // Show warning if APIs are missing
                showMessageBox("Your browser does not fully support camera access. Live scanning may not work. Please ensure you are using a modern browser on a secure (HTTPS) connection.");
                return; // Exit if fundamental camera APIs are missing
            }

            try {
                // Configure the Scandit SDK with your license key and engine location
                await ScanditSDK.configure(SCANDIT_LICENSE_KEY, {
                    engineLocation: "https://cdn.jsdelivr.net/npm/scandit-sdk@5.x/build/"
                });

                // Define symbologies to enable, checking if they exist
                const enabledSymbologies = [];
                if (ScanditSDK.Barcode && ScanditSDK.Barcode.Symbology) {
                    // Conditionally add each symbology if it is defined
                    if (ScanditSDK.Barcode.Symbology.EAN13) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.EAN13);
                    if (ScanditSDK.Barcode.Symbology.EAN8) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.EAN8);
                    if (ScanditSDK.Barcode.Symbology.UPCA) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.UPCA);
                    if (ScanditSDK.Barcode.Symbology.UPCE) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.UPCE);
                    if (ScanditSDK.Barcode.Symbology.CODE128) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.CODE128);
                    if (ScanditSDK.Barcode.Symbology.CODE39) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.CODE39);
                    if (ScanditSDK.Barcode.Symbology.QR) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.QR);
                    if (ScanditSDK.Barcode.Symbology.DATA_MATRIX) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.DATA_MATRIX);
                    if (ScanditSDK.Barcode.Symbology.ITF) enabledSymbologies.push(ScanditSDK.Barcode.Symbology.ITF);
                } else {
                    console.warn("ScanditSDK.Barcode.Symbology not fully loaded. Scanner might not function correctly.");
                    showMessageBox("Scandit SDK symbologies not fully loaded. Functionality might be limited.");
                }

                // Define camera settings, checking for CameraResolution and CameraFacing availability
                let cameraSettings = {};

                if (ScanditSDK.BarcodePicker && ScanditSDK.BarcodePicker.CameraResolution && ScanditSDK.BarcodePicker.CameraResolution.HD) {
                    cameraSettings.preferredResolution = ScanditSDK.BarcodePicker.CameraResolution.HD;
                } else {
                    console.warn("ScanditSDK.BarcodePicker.CameraResolution.HD not available. Using default camera resolution.");
                }

                // Prioritize BACK camera, then ENVIRONMENT, otherwise use default
                if (ScanditSDK.BarcodePicker && ScanditSDK.BarcodePicker.CameraFacing) {
                    if (ScanditSDK.BarcodePicker.CameraFacing.BACK) {
                        cameraSettings.cameraFacing = ScanditSDK.BarcodePicker.CameraFacing.BACK;
                    } else if (ScanditSDK.BarcodePicker.CameraFacing.ENVIRONMENT) {
                        cameraSettings.cameraFacing = ScanditSDK.BarcodePicker.CameraFacing.ENVIRONMENT;
                    } else {
                        console.warn("ScanditSDK.BarcodePicker.CameraFacing.BACK or ENVIRONMENT not available. Using default camera facing.");
                    }
                } else {
                    console.warn("ScanditSDK.BarcodePicker.CameraFacing not fully loaded. Using default camera facing.");
                }

                // Create a new BarcodePicker instance
                const barcodePicker = await ScanditSDK.BarcodePicker.create(
                    document.getElementById("scandit-barcode-picker"), {
                        // Configure the picker to start the camera automatically
                        guiStyle: ScanditSDK.BarcodePicker.GuiStyle.VIEWFINDER,
                        scanSettings: new ScanditSDK.ScanSettings({
                            enabledSymbologies: enabledSymbologies, // Use the dynamically populated array
                            // Enable continuous scanning if desired, or set to false for single scan
                            // For this example, we'll keep it as a single scan then stop
                            // and allow it to be resumed.
                            continuousMode: false
                        }),
                        // Set camera settings to prefer a specific resolution or facing mode
                        cameraSettings: cameraSettings // Use the dynamically configured camera settings
                    }
                );

                // Add a listener for when a barcode is scanned
                barcodePicker.on("scan", (scanResult) => {
                    if (scanResult.barcodes.length > 0) {
                        const scannedBarcode = scanResult.barcodes[0];
                        document.getElementById("scannedBarcode").textContent = scannedBarcode.data;
                        showMessageBox(`Scanned Barcode: ${scannedBarcode.data} (${scannedBarcode.symbology})`);
                        // Pause the scanner after a successful scan
                        barcodePicker.pauseScanning();
                    }
                });

                // Start the camera
                await barcodePicker.resumeScanning();
                console.log("Scandit BarcodePicker started successfully.");

            } catch (error) {
                console.error("Error initializing Scandit BarcodePicker:", error);
                let errorMessage = "Failed to initialize barcode scanner. Please ensure camera access is granted and your license key is valid.";
                if (error.name === "NotReadableError") {
                    errorMessage = "Camera not accessible. This often happens if another app is using the camera, or if permissions are not granted. Please ensure no other application is using the camera and grant camera permissions.";
                } else if (error.name === "NotAllowedError") {
                    errorMessage = "Camera access denied. Please allow camera permissions in your browser settings. This app requires camera access for live scanning.";
                } else if (error.name === "SecurityError") {
                    errorMessage = "Camera access blocked by security policy. Please ensure you are accessing this page over HTTPS (secure connection).";
                } else {
                    errorMessage = `An unexpected error occurred: ${error.message || error}. Please check console for details.`;
                }
                showMessageBox(errorMessage);
            }
        });
    </script>
</body>
</html>

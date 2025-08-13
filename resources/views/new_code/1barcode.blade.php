<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Scandit CDN Simple sample</title>
    <script type="importmap">
      {
        "imports": {
          "@scandit/web-datacapture-core": "https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-core@7.3.0/build/js/index.js",
          "@scandit/web-datacapture-barcode": "https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-barcode@7.3.0/build/js/index.js",

          "@scandit/web-datacapture-barcode/": "https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-barcode@7.3.0/",
          "@scandit/web-datacapture-core/": "https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-core@7.3.0/"
        }
      }
    </script>
    <link
      rel="modulepreload"
      href="https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-core@7.3.0/build/js/index.js"
    />
    <link
      rel="modulepreload"
      href="https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-barcode@7.3.0/build/js/index.js"
    />
    <style>
      html,
      body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: sans-serif;
      }
      #app {
        height: calc(100% - 100px); /* Adjust height to make space for button and data display */
        position: relative;
      }
      #controls {
        padding: 10px;
        text-align: center;
        height: 80px; /* Space for controls */
      }
      #scannedDataDisplay {
        margin-top: 10px;
        font-size: 1.2em;
        color: #333;
        word-wrap: break-word; /* Ensure long data wraps */
      }
      button {
        padding: 10px 20px;
        font-size: 1em;
        cursor: pointer;
      }
    </style>
    <script
      async
      src="https://ga.jspm.io/npm:es-module-shims@1.10.0/dist/es-module-shims.js"
    ></script>
    <script type="module">
      import {
        configure,
        DataCaptureView,
        Camera,
        DataCaptureContext,
        FrameSourceState,
      } from "@scandit/web-datacapture-core";

      import {
        barcodeCaptureLoader,
        BarcodeCaptureSettings,
        BarcodeCapture,
        Symbology,
        SymbologyDescription,
      } from "@scandit/web-datacapture-barcode";

      let view;
      let camera;
      let context;
      let barcodeCapture;
      let scannedDataDisplay;
      let isScanningEnabled = false;

      async function initializeScanner() {
        if (isScanningEnabled) {
          console.log("Scanner already initialized and enabled.");
          return;
        }

        view = new DataCaptureView();
        view.connectToElement(document.getElementById("app"));
        view.showProgressBar();

        await configure({
          licenseKey: "Aj2m9DWeEyiFNnXuOeFIe35Bril4JNi+cgvyvmo0Cgw6fNuKAzSvSQsB1wXobh7zjGSfkKpTMVwQSkdMYmvIoN17LhFeUDY7ui1gRosyeTI+Ey5PgQfhWb8VDrKPaw2gpHHWPVZ5OkxzRBo2JVcikBhyCJp0eoEqUXquOmJOYB+AUVZn1kQJkO53+idZYYiaTVvCL1BiCeUoaYKCOHjBJjN/wiYIR/tXqlrz+FlaK7GRZZsjzmhHfHVQH4m/UsQGhHaGhltLY/ESZ/lcWXy+QVhjR8K5TfF3oRHLzKVTi8SpR0p922KiH1tDgJq3S0g/NlyH0KpNrGxFacn0RF5htoVg88B+cQjto0nT341yuuRPewRpYFP4TWtUkVYddMMxDl8vGQZIq7WrVu4cpxv5Smd0fwigdR0flWJYHi5iBd8uRukOj12Jk0B1hFbTKFFmWGQ+gy9D5LgpeOsesResHoBJpA+pJPREdU8Ap+ZbMOtqKXcQzkV2qFsgt75nWd0LaEiDtCFoztz2C/H7TiRzW5I27mmSm7gyUF1tQSyOX2ifzaXh1tQuJeMyI1y+48AmMWdc2fwJgyuWwH02eelRNE/L1DRjVC8SRC5OgBNwaZNmCuBrP1tt5bNehWIOiIXf2Sh7/QL0hsj+FzejLwgkAhAbshZNfOZ8LSjkVr1EuAVr3oItkKlMHxmOYtj2VnIu0JNOn5ighkHxe6zbISCX3CV6ij5nbeSRUvZ5zfY86pe0ql3q8MT35tX6s2UxFnoqBvnP7H3q/LWI5t/oBRLTilE29LCSswIIL19jPty5giHTQB/8RC5mPx/Zwc0WkjzMFBVicvqtVVolSdpIVx9D19Az4iEGbkwkU2e37RYpbMVIiiUaOgKQaYRHZJS812S6Zu2QITExCfvWnamwh7L//Bo40wWoakOtvwwRK35jmUa5Fv5Z7xgZxD5gM90GVLUOPhlxEHCSelCvrfm8vmnh89SVSTg9pCoYhbitTSzIXb+jE7fMQnftLWXYlb2AT+8+xy8BQyIK/AxAmYYcHI+nVHWCEigoGBwsI7C1+7c/bq7efwL2FDlxqGnU7kp60CWzcgO9y31VWS+6lRJiCFxKU+cflXjT/VB0oCMkc8Z2AqslvlGhB4ZB4o5uUkX5gjv87DMBPuXyzLzceHwZHSZnpG115tO/FVlVet7BZZ9MSp1GsdLZSwK+JaovbGv750RyAIw=",
          libraryLocation:
            "https://cdn.jsdelivr.net/npm/@scandit/web-datacapture-barcode@7.3.0/sdc-lib/",
          moduleLoaders: [barcodeCaptureLoader()],
        });
        view.hideProgressBar();

        camera = Camera.default;

        context = await DataCaptureContext.create();
        await view.setContext(context);

        const cameraSettings = BarcodeCapture.recommendedCameraSettings;
        await camera.applySettings(cameraSettings);

        await context.setFrameSource(camera);
        await context.frameSource.switchToDesiredState(FrameSourceState.On);

        const settings = new BarcodeCaptureSettings();
        settings.enableSymbologies([Symbology.Code128, Symbology.QR]);

        barcodeCapture = await BarcodeCapture.forContext(context, settings);

        barcodeCapture.addListener({
          didScan: async (barcodeCaptureMode, session) => {
            const barcode = session.newlyRecognizedBarcode;
            if (!barcode) {
              return;
            }
            const symbology = new SymbologyDescription(barcode.symbology);
            const scannedData = barcode.data ?? "";
            const readableSymbology = symbology.readableName;

            // --- ADDED: Alert on barcode scanned ---
            alert(`Scanned: ${scannedData}\nSymbology: ${readableSymbology}`);
            // ------------------------------------

            // Display scanned data on screen
            scannedDataDisplay.textContent = `Scanned: ${scannedData} (${readableSymbology})`;

            // Perform AJAX call - Example (replace with your actual API endpoint)
            try {
              const response = await fetch("YOUR_API_ENDPOINT_HERE", {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                },
                body: JSON.stringify({
                  barcodeData: scannedData,
                  symbology: readableSymbology,
                  timestamp: new Date().toISOString(), // Add a timestamp
                }),
              });

              if (response.ok) {
                const result = await response.json();
                console.log("Barcode data sent successfully!", result);
              } else {
                console.error("Failed to send barcode data:", response.status, response.statusText);
              }
            } catch (error) {
              console.error("Error during AJAX call:", error);
            }
          },
        });

        await barcodeCapture.setEnabled(true);
        isScanningEnabled = true;
        console.log("Scanner initialized and enabled.");
      }

      function stopScanner() {
        if (!isScanningEnabled) {
          console.log("Scanner is not enabled.");
          return;
        }
        barcodeCapture.setEnabled(false);
        context.setFrameSource(null);
        view.setContext(null);
        view.destroy();
        barcodeCapture.dispose();
        context.dispose();
        isScanningEnabled = false;
        scannedDataDisplay.textContent = "Scanner stopped. No barcode scanned.";
        console.log("Scanner stopped.");
      }

      // Initialize the scanner automatically when the page loads
      document.addEventListener("DOMContentLoaded", () => {
        scannedDataDisplay = document.getElementById("scannedDataDisplay");
        const stopScannerButton = document.getElementById("stopScanner");

        stopScannerButton.addEventListener("click", stopScanner);

        // Start scanner automatically
        initializeScanner();
      });
    </script>
  </head>
  <body>
    <div id="app"></div>
    <div id="controls">
      <button id="stopScanner">Stop Scanner</button>
      <div id="scannedDataDisplay">Initializing scanner...</div>
    </div>
  </body>
</html>
// Import necessary Scandit SDK modules
import { configure, BarcodePicker, ScanSettings, Barcode } from '@scandit/web-datacapture-barcode';
// If you need core features, you might also import from '@scandit/web-datacapture-core'

// YOUR SCANDIT LICENSE KEY
const scanditLicenseKey = 'AtwWSBmeP+9cHwwd4tsja/oHuoXVKPXOuiRKz9c49zukembuFT9mrF9z4mrhTcZIDEvCBVFl+48nVAi2WRIfPV16fbKtddELjxISK3Mwgb7uC14zqCkI6xsOmrCtM6+lwnHQCIJ7LmSincGv8TFFb/tNHE_/_blovzmKh6H1NT4oLfm2diR6mHXFPlSHeQX+r1loNpggRoXTHSG2YB0cuz9pie7BRenKUpXmsfoIVgNZWSIw9M2dqPilU04iWQO3Bynb4tZF7iXsRddq39VEiTugmF5yycfpUOHjiZ7tbZd/aaznxZ2OOxORin0gaepVahGLlSDpSXLRHSstAgWAEmQhKDshQDhYPkUvBXVVbgizgR2tsbmpGPPxWZusubsCZv3sut9NQjnuFfJBsCVP3UtZrbjoZb/RJn22iVq9LpJSzUcQmSEg+PrN7J3rMe16S5nGwSCoEAQzBS1MxVk2LHm5NGpegJB3Eo28GbfoGTD0Xe6q5FGm8AjAYfE3ScdiWHH/HdZg2t/EdRopPni4TkIcfcj+lXFf18HS1b5hjdBqL9STjChJasJChHnn5Ar2nTiLDY47tbo21hxO/kCvMz8VUVhH6aULICtkDLTNZ4h5TyDf2UNJimtz5bXh49EFuIMwgTvgS5PFnGOce2StwaNwPfy3JfX_P4cmK5ZKuNJxowz4Hs44AsRwk3iWoztQ1ckZ6AtWWoSgjPotdiBfmN9Jnt3/f5sMCsHT4ZQRRICbjbn8Ee9EkdZ_yfw3yDIAURxR1XxGCwxSUU0mMZxqoObD0i2ai9LFxMCjWPo9lViflan8xesX0Zx60dfz4TD4xlmN2cjn6MVGqLkqRD_XKWWemIkrX_jNflzWka9AFaydg3XhxqHcVIPMQfSDqw79YmN1Wlpw+Pp3AIE9UgVEcLVZaSdVqLpfwB0aTqsD5+JXu_sH+0qnceXAlXq4UgBUvBXywOLwvfViv7oscyAZ10aKF47799T3N3T55D2z5ppKt52tfYRIQiPjbpdS5PY7l2tqR8jD4rIrfC4bz7s_8dQcVNqn_2OD6MoTT8ED1CFxGoEHU8VzCWf0Q7nx8IfwTfeT7fok8GqFgKhboi8psepXGlT3gHCuNlsQQLFG_UzBoFQhMJQYgeMjHNS6vtEcXy1gKoRHyzBHG8yntswU+0MO3hzsCjXFW8i4pYDM0bJsy2DIIGNIcqdfBLcDB27EF4M=';

configure(scanditLicenseKey, {
    // This path is now relative to your public directory where engine files are copied
    engineLocation: "/js/scandit-engine"
});

let barcodePicker = null;
const startButton = document.getElementById('startButton');
const stopButton = document.getElementById('stopButton');
const scannedBarcodeSpan = document.getElementById('scannedBarcode');
const symbologySpan = document.getElementById('symbology');
const scannerStatusSpan = document.getElementById('scanner-status');

async function startScanner() {
    if (barcodePicker) {
        console.log("Scanner already running, resuming...");
        barcodePicker.resumeScanning();
        startButton.classList.add('hidden');
        stopButton.classList.remove('hidden');
        scannerStatusSpan.textContent = 'Scanning...';
        return;
    }

    scannerStatusSpan.textContent = 'Starting scanner...';
    try {
        barcodePicker = await BarcodePicker.create(document.getElementById("scandit-container"), {
            guiStyle: BarcodePicker.GuiStyle.VIEWFINDER,
            recognitionMode: BarcodePicker.RecognitionMode.CONTINUOUS,
            scanSettings: new ScanSettings({
                enabledSymbologies: [
                    Barcode.Symbology.EAN8,
                    Barcode.Symbology.EAN13,
                    Barcode.Symbology.UPCA,
                    Barcode.Symbology.UPCE,
                    Barcode.Symbology.CODE128,
                    Barcode.Symbology.CODE39,
                    Barcode.Symbology.QR,
                    Barcode.Symbology.DATA_MATRIX,
                    Barcode.Symbology.PDF417,
                    Barcode.Symbology.ITF,
                    Barcode.Symbology.AZTEC
                ],
            })
        });

        barcodePicker.on("scan", (scanResult) => {
            if (scanResult.barcodes.length > 0) {
                const barcode = scanResult.barcodes[0];
                scannedBarcodeSpan.textContent = barcode.data;
                symbologySpan.textContent = barcode.symbology;
                console.log(`Scanned: ${barcode.data} (${barcode.symbology})`);
            }
        });

        barcodePicker.on("ready", () => {
            console.log("BarcodePicker is ready!");
            scannerStatusSpan.textContent = 'Ready to scan!';
            startButton.classList.add('hidden');
            stopButton.classList.remove('hidden');
            barcodePicker.resumeScanning();
            scannerStatusSpan.textContent = 'Scanning...';
        });

        barcodePicker.on("error", (error) => {
            console.error("Scandit BarcodePicker Error:", error);
            scannerStatusSpan.textContent = `Error: ${error.message}`;
            alert("Error initializing scanner: " + error.message + ". Check console for details.");
            startButton.classList.remove('hidden');
            stopButton.classList.add('hidden');
        });

    } catch (error) {
        console.error("Failed to create BarcodePicker:", error);
        scannerStatusSpan.textContent = `Error: ${error.message}`;
        alert("Failed to initialize scanner. Make sure your browser supports camera access and check console for details.");
        startButton.classList.remove('hidden');
        stopButton.classList.add('hidden');
    }
}

function stopScanner() {
    if (barcodePicker) {
        barcodePicker.dispose();
        barcodePicker = null;
        console.log("Scanner stopped and disposed.");
        startButton.classList.remove('hidden');
        stopButton.classList.add('hidden');
        scannedBarcodeSpan.textContent = 'N/A';
        symbologySpan.textContent = 'N/A';
        scannerStatusSpan.textContent = 'Stopped.';
    }
}

startButton.addEventListener('click', startScanner);
stopButton.addEventListener('click', stopScanner);

window.addEventListener('beforeunload', () => {
    if (barcodePicker) {
        barcodePicker.dispose();
    }
});
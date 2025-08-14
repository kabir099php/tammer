@extends('layouts.vendor.app')

@section('title',translate('messages.QR Code Generator'))

@push('css_or_js')
<style>
    /* Basic styling for the container and elements */
    .qr-code-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background-color: #f9fafb; /* gray-50 */
        border: 1px solid #e5e7eb; /* gray-200 */
        border-radius: 0.5rem; /* rounded-lg */
        margin-top: 2rem;
    }
    .qr-code-canvas-wrapper {
        padding: 0.5rem;
        background-color: #ffffff; /* white */
        border-radius: 0.375rem; /* rounded-md */
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); /* shadow-inner */
    }
    .download-button {
        margin-top: 1.5rem;
        width: 100%;
        padding: 0.75rem 1.5rem;
        background-color: #2563eb; /* blue-600 */
        color: #ffffff; /* white */
        font-weight: 600; /* font-semibold */
        border-radius: 0.375rem; /* rounded-md */
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); /* shadow-md */
        transition: all 0.2s ease-in-out;
        transform: scale(1);
    }
    .download-button:hover {
        background-color: #1d4ed8; /* blue-700 */
        transform: scale(1.05);
    }
    .download-button:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.5), 0 0 0 2px rgba(255, 255, 255, 1); /* focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 */
    }
    .form-control-select {
        display: block;
        width: 100%;
        padding: 0.5rem 1rem;
        padding-right: 2.5rem; /* for custom arrow */
        border-radius: 0.375rem;
        border: 1px solid #d1d5db; /* gray-300 */
        background-color: #ffffff;
        color: #111827; /* gray-900 */
        -webkit-appearance: none; /* remove default arrow */
        -moz-appearance: none;
        appearance: none;
        transition: all 0.2s ease-in-out;
    }
    .form-control-select:focus {
        outline: none;
        border-color: transparent;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* focus:ring-2 focus:ring-blue-500 */
    }
    .relative-select::after {
        content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'%3E%3C/path%3E%3C/svg%3E");
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #4b5563; /* gray-700 */
        height: 1rem;
        width: 1rem;
    }
    .empty-data-message {
        margin-top: 2rem;
        padding: 1rem;
        text-align: center;
        color: #6b7280; /* gray-500 */
        background-color: #eff6ff; /* blue-50 */
        border: 1px solid #bfdbfe; /* blue-200 */
        border-radius: 0.5rem;
    }
</style>
@endpush


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    {{-- Using a placeholder image for the icon as per instructions --}}
                    <img src="https://placehold.co/26x26/FF6F00/FFFFFF?text=QR" class="w--26" alt="QR Code Icon">
                </span>
                <span>
                    {{translate('messages.QR Code Generator')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="qr_option" class="form-label">{{translate('Select QR Code Type')}}:</label>
                                    <div class="relative-select">
                                        <select id="qr_option" class="form-control-select">
                                            <option value="">{{translate('Select')}}</option>
                                            <option value="checkout">{{translate('Checkout')}}</option>
                                            <option value="scanner">{{translate('Scanner')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $vendor_id = \App\CentralLogics\Helpers::get_loggedin_user()->id;   @endphp
    
                        {{-- Placeholder message if no option is selected --}}
                        <div id="placeholder_message" class="empty-data-message">
                            <p class="font-medium">{{translate('Please select an option from the dropdown to generate a QR code.')}}</p>
                        </div>

                        {{-- QR Code Display Area --}}
                        <div id="qr_code_display_area" class="qr-code-container" style="display: none;">
                            <div id="qr_code_canvas_wrapper" class="qr-code-canvas-wrapper">
                                <img id="dummy_qr_img" src="https://placehold.co/150x150.png?text=QR" alt="QR Code">
                            </div>
                            <button id="download_qr_code_btn" class="btn btn-sm btn-primary mt-3">
                            {{ translate('Download QR Code') }}
                        </button>
                            <div class="mt-3 text-sm text-center">
                                <strong>{{ translate('URL:') }}</strong>
                                <span id="qr_code_value_display" class="ml-2"></span>
                                <button id="copy_url_btn" class="ml-2 btn btn-sm btn-light">
                                    {{ translate('Copy') }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@php
    

    use App\Models\Store; // You need to import the model if you want to use Eloquent
    // Or use Illuminate\Support\Facades\DB; for Query Builder

    
    $store = Store::where('vendor_id', $vendor_id)->first();
    $store_name = $store->slug;
    
@endphp

@push('script_2')
    {{-- Load qrcode.js library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode.js/1.0.0/qrcode.min.js"></script>
    
    <script>
        const store_name = @json($store_name);
        // Ensure all resources, including external scripts, are loaded before running logic
        window.onload = function() {
            const qrOptionSelect = document.getElementById('qr_option');
            const qrCodeDisplayArea = document.getElementById('qr_code_display_area');
            const qrCodeCanvasWrapper = document.getElementById('qr_code_canvas_wrapper');
            const qrCodeValueDisplay = document.getElementById('qr_code_value_display');
            const downloadQrCodeBtn = document.getElementById('download_qr_code_btn');
            const placeholderMessage = document.getElementById('placeholder_message');

            let qrCodeInstance = null; // To store the QR code instance for cleanup

            const urls = {
                checkout: `https://${store_name}.waslqr.com/details`,
                scanner: '{{env('APP_URL')}}/barcode',
            };

           function generateQRCode(value) {
    if (!value) {
        placeholderMessage.style.display = 'block';
        qrCodeDisplayArea.style.display = 'none';
        return;
    }
    

    placeholderMessage.style.display = 'none';
    qrCodeDisplayArea.style.display = 'flex';

    // Dummy QR image update
    const dummyImg = document.getElementById('dummy_qr_img');
    dummyImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(value)}`;

    // Display URL text
    qrCodeValueDisplay.textContent = value;
}

// Dropdown change
qrOptionSelect.addEventListener('change', function () {
    const selectedValue = this.value;
    let valueToEncode = '';

    if (selectedValue === 'checkout') {
        document.getElementById('qr_code_canvas_wrapper').style.display = 'none';
        document.getElementById('dummy_qr_img').style.display = 'none';
        document.getElementById('download_qr_code_btn').style.display = 'none';
        
        valueToEncode = urls.checkout;
    } else if (selectedValue === 'scanner') {
        document.getElementById('qr_code_canvas_wrapper').style.display = 'flex';
        document.getElementById('dummy_qr_img').style.display = 'flex';
        document.getElementById('download_qr_code_btn').style.display = 'flex';
        valueToEncode = urls.scanner;
    }

    generateQRCode(valueToEncode);
});

// Copy to clipboard functionality
document.getElementById('copy_url_btn').addEventListener('click', function () {
    const text = qrCodeValueDisplay.textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('{{ translate('URL copied to clipboard!') }}');
    });
});

// Download QR code
downloadQrCodeBtn.addEventListener('click', function () {
    const dummyImg = document.getElementById('dummy_qr_img');
    const link = document.createElement('a');
    link.href = dummyImg.src;
    link.download = `${qrOptionSelect.value}_qrcode.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});
            // Initial state: ensure placeholder is shown if no option is selected by default
            // This also handles cases where a value might be pre-selected on page load.
            const initialSelectedValue = qrOptionSelect.value;
            let initialValueToEncode = '';
            if (initialSelectedValue === 'checkout') {
            
                
            
    
                initialValueToEncode = urls.checkout;
            } else if (initialSelectedValue === 'scanner') {
                initialValueToEncode = urls.scanner;
            }
            generateQRCode(initialValueToEncode); // Generate QR code on initial load if an option is selected
        };
    </script>
@endpush

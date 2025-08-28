@extends('layouts.vendor.app')

@section('title',translate('messages.Setting'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/fi_9752284.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.Setting')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ url('vendor-panel/settings-vendor/vendor-update-setting') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- <div class="col-12 d-flex justify-content-end">

                                    <div class="blinkings">
                                        <strong class="mr-2">{{translate('instructions')}}</strong>
                                        <div>
                                            <i class="tio-info-outined"></i>
                                        </div>
                                        <div class="business-notes">
                                            <h6><img src="{{asset('/public/assets/admin/img/notes.png')}}" alt=""> {{translate('Note')}}</h6>
                                            <div>
                                                {{translate('messages.Customer_will_see_there_banners_in_your_store_details_page_in_website_and_user_apps.')}}
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <label for="title" class="form-label">{{translate('Detail Page Footer')}}</label>
                                        <input id="title" type="text" name="detail_page_footer" class="form-control" placeholder="{{translate('messages.Detail Page Footer')}}" value="{{$vendor->detail_page_footer}}" required>
                                    </div>
                                   
                            </div>
                            <div class="btn--container justify-content-end mt-3">
                                
                                <button type="submit" class="btn btn--primary mb-2" style="min-width: 100px;max-height: 50px;">{{translate('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            <!-- End Table -->
        </div>
    </div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateCodeBtn = document.getElementById('generate_code_btn');
        const codeInput = document.getElementById('code_input');

        function generateRandomCode(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        // Add event listener to the button
        generateCodeBtn.addEventListener('click', function() {
            const randomCode = generateRandomCode(8); // Generate an 8-character code
            codeInput.value = randomCode; // Set the generated code to the input field
        });

        // Optionally, generate a code on page load
        // const initialRandomCode = generateRandomCode(8);
        // codeInput.value = initialRandomCode;
    });
</script>

@push('script_2')
        <script>
            "use strict";
            $('#reset_btn').click(function(){
                $('#viewer').attr('src','{{asset('/public/assets/admin/img/upload-4.png')}}');
            })
        </script>

@endpush

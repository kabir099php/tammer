@extends('layouts.vendor.app')

@section('title',translate('messages.POS-Device'))

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
                    {{translate('messages.POS-Device')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('vendor.pos-device.store') }}" method="POST" enctype="multipart/form-data">
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

                                        <label for="title" class="form-label">{{translate('terminal_id')}}</label>
                                        <input id="title" type="text" name="terminal_id" class="form-control" placeholder="{{translate('messages.terminal_id...')}}" required>
                                    </div>
                                    <div class="form-group">

                                        <label for="title" class="form-label">{{translate('code')}}</label> 
                                        <div style="display:flex">
                                        <input id="code_input" type="text" name="code" class="form-control" placeholder="{{translate('Click to generate code')}}" readonly>
                                        <button type="button" id="generate_code_btn" class="btn btn-primary">{{translate('Generate')}}</button>
                                        </div>
                                        

                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{translate('Connection Status')}}</label>
                                        <label class="toggle-switch toggle-switch-sm" for="connection_status_toggle">
                                            <input type="checkbox" name="connection_status" class="toggle-switch-input" id="connection_status_toggle" value="1" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <small class="form-text text-muted">{{translate('Toggle to set the connection status of the POS device. (Active/Inactive)')}}</small>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-sm-6">
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-3">
                                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('Reset')}}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{translate('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.pos_device_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$pos_device->count()}}</span>
                            </h5>
                            <form id="search-form" class="search-form">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{translate('messages.search_by_title')}}" aria-label="{{translate('messages.search_here')}}" value="{{ request()->search }}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true,
                                "search": "#datatableSearch",
                                "entries": "#datatableEntries",
                                "isResponsive": false,
                                "isShowPaging": false,
                                "paging": false
                               }'
                               >
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{ translate('messages.SL') }}</th>
                                    <th class="border-0">{{translate('messages.terminal_id')}}</th>
                                    <th class="border-0">{{translate('messages.code')}}</th>
                                    
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($pos_device as $key=>$banner)
                                <tr>
                                    <td>{{$key+$pos_device->firstItem()}}</td>
                                    <td><h5 class="text-hover-primary mb-0">{{$banner['terminal_id']}}</h5></td>
                                    <td><h5 class="text-hover-primary mb-0">{{$banner['code']}}</h5></td>
                                    
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <!--  -->
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                               data-id="banner-{{$banner['id']}}"
                                               data-message="{{ translate('Want to delete this device ?') }}"
                                                title="{{translate('messages.delete_banner')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('vendor.pos-device.delete',[$banner['id']])}}"
                                                        method="post" id="banner-{{$banner['id']}}">
                                                    @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @if(count($pos_device) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $pos_device->links() !!}
                        </div>
                        @if(count($pos_device) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                </div>
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

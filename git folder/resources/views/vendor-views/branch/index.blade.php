@extends('layouts.vendor.app')

@section('title',translate('messages.Branch'))

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
                    {{translate('messages.Branch')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('vendor.branch.store') }}" method="POST" enctype="multipart/form-data">
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

                                        <label for="title" class="form-label">{{translate('Branch_title')}}</label>
                                        <input id="title" type="text" name="name" class="form-control" placeholder="{{translate('messages.title_here...')}}" required>
                                    </div>
                                    <div class="form-group">

                                        <label for="title" class="form-label">{{translate('Branch_title_ar')}}</label>
                                        <input id="title" type="text" name="name_ar" class="form-control" placeholder="{{translate('messages.title_here...')}}" required>
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
                                {{translate('messages.Branch_List')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$branch->count()}}</span>
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
                                    <th class="border-0">{{translate('messages.name')}}</th>
                                    
                                    
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($branch as $key=>$banner)
                                <tr>
                                    <td>{{$key+$branch->firstItem()}}</td>
                                    <td><h5 class="text-hover-primary mb-0">{{Str::limit($banner['name'], 25, '...')}}</h5></td>
                                    
                                    
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <!--  -->
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                               data-id="banner-{{$banner['id']}}"
                                               data-message="{{ translate('Want to delete this branch ?') }}"
                                                title="{{translate('messages.delete_banner')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('vendor.branch.delete',[$banner['id']])}}"
                                                        method="post" id="banner-{{$banner['id']}}">
                                                    @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @if(count($branch) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $branch->links() !!}
                        </div>
                        @if(count($branch) === 0)
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

@push('script_2')
        <script>
            "use strict";
            $('#reset_btn').click(function(){
                $('#viewer').attr('src','{{asset('/public/assets/admin/img/upload-4.png')}}');
            })
        </script>

@endpush

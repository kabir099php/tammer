<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;


class QrCodeController extends Controller
{
    function list(Request $request)
    {
        $key = explode(' ', $request['search']);
        $branch = Branch::where('store_id',Helpers::get_store_id())->latest()->paginate(config('default_pagination'));
        return view('vendor-views.QRCode.index',compact('branch'));
    }


}

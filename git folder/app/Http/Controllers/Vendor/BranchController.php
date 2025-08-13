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


class BranchController extends Controller
{
    function list(Request $request)
    {
        $key = explode(' ', $request['search']);
        $branch = Branch::where('store_id',Helpers::get_store_id())->latest()->paginate(config('default_pagination'));
        return view('vendor-views.branch.index',compact('branch'));
    }


    public function status(Request $request)
    {
        $banner = Branch::findOrFail($request->id);
        $store_id = $request->status;
        $store_ids = json_decode($banner->restaurant_ids);
        if(in_array($store_id, $store_ids))
        {
            unset($store_ids[array_search($store_id, $store_ids)]);
        }
        else
        {
            array_push($store_ids, $store_id);
        }

        $banner->restaurant_ids = json_encode($store_ids);
        $banner->save();
        Toastr::success(translate('messages.capmaign_participation_updated'));
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $store = Helpers::get_store_data();
        $branch = new Branch;
        $branch->name = $request->name;
        $branch->name_ar = $request->name_ar;
        $branch->store_id = $store->id;
        $branch->save();
        Toastr::success(translate('messages.branch_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $branch = Branch::withoutGlobalScope('translate')->findOrFail($id);
        return view('vendor-views.branch.edit', compact('branch'));
    }

    public function status_update(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success(translate('messages.banner_status_updated'));
        return back();
    }
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required',
            'default_link' => 'max:255',

        ]);
        $banner->title = $request->title;
        $banner->image = $request->has('image') ? Helpers::update('banner/', $banner->image, 'png', $request->file('image')) : $banner->image;
        $banner->default_link = $request->default_link;
        $banner->save();
        Toastr::success(translate('messages.banner_updated_successfully'));
        return back();
    }

    public function delete(Request $request , $id)
    {
   
        //Helpers::check_and_delete('banner/' , $banner['image']);
        //$branch = new Branch;
        
        $branch = Branch::find($id);
        $branch->delete();
        Toastr::success(translate('messages.branch_deleted_successfully'));
        return back();
    }

    // public function search(Request $request){
    //     $key = explode(' ', $request['search']);
    //     $banners=Banner::where('data',Helpers::get_store_id())->where('created_by','store')->where(function ($q) use ($key) {
    //         foreach ($key as $value) {
    //             $q->orWhere('title', 'like', "%{$value}%");
    //         }
    //     })->limit(50)->get();
    //     return response()->json([
    //         'view'=>view('vendor-views.banner.partials._table',compact('banners'))->render(),
    //         'count'=>$banners->count()
    //     ]);
    // }

}

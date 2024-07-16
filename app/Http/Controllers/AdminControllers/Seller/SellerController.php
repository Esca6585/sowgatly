<?php

namespace App\Http\Controllers\AdminControllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Http\Requests\SellerRequest;
use Illuminate\Support\Facades\Hash;
use Str;

class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $lang, $pagination = 10)
    {
        if($request->pagination) {
            $pagination = (int)$request->pagination;
        }

        $sellers = Seller::orderByDesc('id')->paginate($pagination);

        if(request()->ajax()){
            if($request->search) {
                $searchQuery = trim($request->query('search'));
                
                $requestData = Seller::fillableData();
    
                $sellers = Seller::where(function($q) use($requestData, $searchQuery) {
                                        foreach ($requestData as $field)
                                        $q->orWhere($field, 'like', "%{$searchQuery}%");
                                })->paginate($pagination);
            }
            
            return view('admin-panel.seller.seller-table', compact('sellers', 'pagination'))->render();
        }

        return view('admin-panel.seller.seller', compact('sellers', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($lang, Seller $seller)
    {
        return view('admin-panel.seller.seller-form', compact('seller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($lang, SellerRequest $request)
    {   
        $seller = new Seller;

        $this->uploadImage($seller, $request);
        
        $seller->name = $request->name;
        $seller->phone_number = $request->phone_number;
        $seller->status = $request->status;
        $seller->password = Hash::make($request->password);

        $seller->save();

        return redirect()->route('seller.index', app()->getlocale() )->with('success-create', 'The resource was created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function show($lang, Seller $seller)
    {
        return view('admin-panel.seller.seller-show', compact('seller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function edit($lang, Seller $seller)
    {
        return view('admin-panel.seller.seller-form', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function update($lang, SellerRequest $request, Seller $seller)
    {
        $this->uploadImage($seller, $request);
        
        $seller->name = $request->name;
        $seller->phone_number = $request->phone_number;
        $seller->status = $request->status;
        $seller->password = Hash::make($request->password);

        $seller->update();

        return redirect()->route('seller.index', app()->getlocale() )->with('success-update', 'The resource was updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang, Seller $seller)
    {
        $this->deleteFolder($seller);

        $seller->delete();

        return redirect()->route('seller.index', [ app()->getlocale() ])->with('success-delete', 'The resource was deleted!');
    }

    public function deleteFolder($seller)
    {
        if($seller->image){
            $folder = explode('/', $seller->image);

            if($folder[1] != 'seller-seeder'){
                \File::deleteDirectory($folder[0] . '/' . $folder[1]);
            }
        }
    }

    public function uploadImage($seller, $request)
    {
        if($request->file('image')){
            $this->deleteFolder($seller);

            $image = $request->file('image');

            $date = date("d-m-Y H-i-s");
            
            $fileRandName = Str::random(10);
            $fileExt = $image->getClientOriginalExtension();

            $fileName = $fileRandName . '.' . $fileExt;
            
            $path = 'seller/' . Str::slug($request->name . '-' . $date ) . '/';

            $image->move($path, $fileName);
            
            $originalImage = $path . $fileName;

            $seller->image = $originalImage;
        }
    }
}

<?php

namespace App\Http\Controllers\AdminControllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Letterhead;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserEditRequest;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Str;

class UserController extends Controller
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

        $users = User::orderByDesc('id')->withTrashed()->paginate($pagination);

        $roles = Role::where('guard_name', 'web')->pluck('name','name')->all();

        if(request()->ajax()){
            if($request->search) {
                $searchQuery = trim($request->query('search'));

                $requestData = User::fillableData();

                $users = User::where(function($q) use($requestData, $searchQuery) {
                                        foreach ($requestData as $field)
                                        $q->orWhere($field, 'like', "%{$searchQuery}%");
                                })->withTrashed()->paginate($pagination);
            }

            return view('admin-panel.user.user-table', compact('users', 'pagination'))->render();
        }

        return view('admin-panel.user.user', compact('users', 'roles', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($lang, User $user)
    {
        $roles = Role::where('guard_name', 'web')->pluck('name','name')->all();

        return view('admin-panel.user.user-form', compact('user', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($lang, UserCreateRequest $request)
    {
        if(User::where('email', $request->email)->exists()) {
            return redirect()->route('user.index', [ app()->getlocale() ])->with('warning', 'This Email Address already is exist!');
        } else {
            $user = new User;

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->company_name = $request->company_name;
            $user->password = Hash::make($request->password);

            $user->save();

            $user->assignRole($request->roles);

            return redirect()->route('user.index', [ app()->getlocale() ])->with('success-create', 'The resource was created!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($lang, $id)
    {
        $user = User::withTrashed()->find($id);
        $roles = Role::where('guard_name', 'web')->pluck('name','name')->all();

        return view('admin-panel.user.user-show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($lang, User $user)
    {
        $roles = Role::where('guard_name', 'web')->pluck('name','name')->all();

        return view('admin-panel.user.user-form', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($lang, UserEditRequest $request, User $user)
    {
        if(User::where('email', $request->email)->where('id', $user->id)->exists()) {

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->company_name = $request->company_name;

            if($request->password){
                $user->password = Hash::make($request->password);
            }

            $user->update();

            DB::table('model_has_roles')->where('model_id', $user->id)->delete();

            $user->assignRole($request->roles);

            $this->addLetterHead($request, $user);

            return redirect()->route('user.index', [ app()->getlocale() ])->with('success-update', 'The resource was updated!');
        }
        return redirect()->route('user.index', [ app()->getlocale() ])->with('warning', 'This Email Address already is exist!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang, User $user)
    {
        $user->delete();

        return redirect()->route('user.index', [ app()->getlocale() ])->with('success-delete', 'The resource was deleted!');
    }

    public function addLetterHead($request, $user)
    {
        $originalLetterhead = env('APP_URL') . '/img/Emblem_of_Turkmenistan.png';
        if($user->letterhead != null){
            $originalLetterhead = $user->letterhead->image;
        }

        if($request->file('image')){
            $this->deleteFolder($user);

            $letterheadLogo = $request->file('image');

            $date = date("d-m-Y_H-i-s");

            $code_number = Str::random(10);

            $user_name = $user->first_name . "_" . $user->last_name . "_" . $user->id;

            $fileRandName = 'logo_' . $code_number;

            $fileExt = $letterheadLogo->getClientOriginalExtension();

            $fileName = $fileRandName . '.' . $fileExt;

            $path = "assets/user/" . $user_name . '/';

            $letterheadLogo->move($path, $fileName);

            $originalLetterhead = $path . $fileName;
        }

        Letterhead::findOrCreate($user->id, [
            'user_id' => $user->id,
            'company_name_tm' => $request->company_name_tm, 
            'company_name_en' => $request->company_name_en, 
            'address_tm' => $request->address_tm,
            'address_en' => $request->address_en,
            'phone_number_tm' => $request->phone_number_tm,
            'phone_number_en' => $request->phone_number_en,
            'email_tm' => $request->email_tm,
            'email_en' => $request->email_en,
            'image' => $originalLetterhead,
        ]);
    }

    public function deleteFolder($user)
    {
        if($user->letterhead){
            if($user->letterhead->image){
                $folder = explode('/', $user->letterhead->image);
    
                if($folder[2] != 'user-seeder'){
                    \File::deleteDirectory($folder[0] . '/' . $folder[1] . '/' . $folder[2]);
                }
            }
        }
    }
}

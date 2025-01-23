<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Facades\Image;

class AccountController extends Controller
{
    // This method will show register page
    public function register()
    {
        return view('account.register');
    }

    // This method will register the user
    public function processRegister(Request $request)
    {
        $rules = [
            'name'=>'required|min:3',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:3',
            'password_confirmation'=>'required' 
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        // Now Resgiter User

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password); 
        $user->save();

        return redirect()->route('account.login')->with('success','You have Register Successfully.');


    }

    public function login()
    {
        return view('account.login');
    }

    // This Method will show your Profile data
    public function profile()
    {
        $user = User::find(Auth::user()->id);
        // dd($user);
        return view('account.profile',[
            'user'=>$user
        ]);
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email'=>'required|email',
            'password'=>'required|min:3'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->route('account.profile');
        }else{
            return redirect()->route('account.login')->with('error','Either Email or Passwword is incorrect!!');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    // This Method Will Update User Profile
    public function updateProfile(Request $request)
    {
        $rules = [
            'name'=>'required',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
        }

        if(!empty($request->image))
        {
            $rules['image'] = 'image';
        }

        $user = User::find(Auth::user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Here We Upload Image
        if(!empty($request->image)){

            File::delete(public_path('uploads/profile/'.$user->image));
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $image->move(public_path('uploads/profile'),$imageName);
            $user->image = $imageName;
            $user->save();

            // $manager = new ImageManager(Driver::class);
            // $image = $manager->read(public_path('uploads/profile/'.$imageName));

            // $image->cover(150,150);
            // $image->save(public_path('uploads/profile/thumb'.$imageName));
        }
        
        return redirect()->route('account.profile')->with('success','User Details have Successfully Update!!!');
    }
}

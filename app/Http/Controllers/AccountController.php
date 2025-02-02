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
use App\Models\Review;

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

    public function myReviews(Request $request){

        $reviews = Review::with('book')->where('user_id',Auth::user()->id);
        $reviews = $reviews->orderBy('created_at','DESC');

        if(!empty($request->keyword)){
            $reviews = $reviews->where('review','like','%'.$request->keyword.'%');
        }
        $reviews = $reviews->paginate(3);
        return view('account.myreviews.myreviews',compact('reviews'));
    }

    public function editReviews($id){
        $review =Review::where([
            'id'=>$id,
            'user_id'=>Auth::user()->id

        ])->with('book')->first();

        return view('account.myreviews.editreviews',compact('review'));
    }

    public function updateMyReview($id,Request $request){

        $review = Review::findOrFail($id);

        $rules = [
            'review' => 'required|min:10',
            'rating'=>'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.reviews.edit',$id)->withInput()->withErrors($validator);
        }

        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        Session()->flash('success','Review Updated Successgully!!');
        return redirect()->route('account.myReviews');
    }

    public function deleteMyReview(Request $request){
        $id = $request->id;

        $review = Review::find($id);

        if($review == null){
            return response()->json([
                'status'=>false
            ]);
        }

        $review->delete();

        session()->flash('success','Review Deleted Successfully');

        return response()->json([
            'status'=>true,
            'message'=>'Review Delete Successfully'
        ]);

    }

    public function changePassword(){
        $user = User::find(Auth::user()->id);
        // dd($user);
        return view('account.changepassword',compact('user'));
    }

    public function PasswordChangeRequest($id,Request $request){

        $id = $request->id;

        $user = User::find($id);

        if(!$user){
            return redirect()->route('account.changePassword')->with('error','User not Found');
        }
        
        $rules = [
            'old_password' => 'required',
            'password'=>'required|confirmed|min:3',
            'password_confirmation'=>'required' 
        ];

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->route('account.profile')->with('error','Check the Old Pssword');
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.changePassword')->withInput()->withErrors($validator);
        }

        $user->password = Hash::make($request->password); 
        $user->save();

        return redirect()->route('account.profile')->with('success','Password Update Successfully');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $book = Book::orderBy('created_at','DESC');

        if(!empty($request->keyword)){
            $book->where('title','like','%'.$request->keyword.'%');
        }

        $book = $book->where('status',1)->paginate(4);
        
        // dd($book);
        
        return view('home',compact('book'));
    }

    // This Method Will show book details Page
    public function details($id)
    {
        $book = Book::with(['reviews.user','reviews'=> function($query){
            $query->where('status',1);
        }])->findOrFail($id);
        // dd($book);

        $relatedBook = Book::where('status',1)->take(3)->where('id','!=',$id)->inRandomOrder()->get();

        if($book->status == 0)
        {
            abort(404);
        }
        return view('book_detail',compact('book','relatedBook'));
    }

    public function saveReview(Request $request)
    {
        $rules = [
            'review'=>'required|min:10',
            'rating'=>'required'
        ];
        $validator = Validator::make($request->all(),$rules);


        if($validator->fails())
        {
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        $countReview = Review::where('user_id',Auth::user()->id)->where('book_id',$request->book_id)->count();

        if($countReview > 0){
            session()->flash('error','You Already Submitted Review');
            return response()->json([
                'status'=>true,
            ]);
        }

        $review = new Review();

        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->user_id = Auth::user()->id;
        $review->book_id = $request->book_id;
        $review->save();

        session()->flash('success','Review Sunmit successful');

        return response()->json([
            'status'=>true,

        ]);

    }
}

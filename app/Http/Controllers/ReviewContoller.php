<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ReviewContoller extends Controller
{
    public function index(Request $request){
        $reviews = Review::with('book','user')->orderBy('created_at','DESC');

        if(!empty($request->keyword)){
            $reviews = $reviews->where('review','like','%'.$request->keyword.'%');
        }
        $reviews = $reviews->paginate(1);
        return view('account.reviews.list',compact('reviews'));
    }

    // This method will show edit 
    public function edit($id){
        $review = Review::findOrFail($id);

        return view('account.reviews.edit',compact('review'));
    }

    public function updateReview($id,Request $request){

        $review = Review::findOrFail($id);

        $rules = [
            'review' => 'required|min:10',
            'status'=>'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.reviews.edit',$id)->withInput()->withErrors($validator);
        }

        $review->review = $request->review;
        $review->status = $request->status;
        $review->save();

        Session()->flash('success','Review Updated Successgully!!');
        return redirect()->route('account.reviews.list');
    }

    public function deleteReview(Request $request){

        $id = $request->id;
        $review = Review::find($id);

        if($review == null){
            session()->flash('error','Review Not Found');
            return response()->json([
                'status'=>false
            ]);
        }else{
            $review->delete();
            session()->flash('success','Review Deleted Successfully');
            return response()->json([
                'status'=>true
            ]);
        }
    }
}

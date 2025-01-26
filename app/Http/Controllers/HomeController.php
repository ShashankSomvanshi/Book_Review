<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
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
        $book = Book::findOrFail($id);

        $relatedBook = Book::where('status',1)->take(3)->where('id','!=',$id)->inRandomOrder()->get();

        if($book->status == 0)
        {
            abort(404);
        }
        return view('book_detail',compact('book','relatedBook'));
    }
}

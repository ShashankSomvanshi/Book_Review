<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // THis method Will Show Book Listing
    public function index(Request $request)
    {
        $books = Book::orderBy('created_at','DESC');
        if(!empty($request->keyword))
        {
            $books->where('title','like','%'.$request->keyword.'%');
        }
        $books = $books->paginate(3);
        return view('Books.list',[
            'books'=>$books
        ]);
    }

    // This method will create a book list
    public function create()
    {
        return view('Books.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title'=>'required|min:3',
            'author'=>'required|min:3',
            'status'=>'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->route('book.create')->withInput()->withErrors($validator);
        }

        if(!empty($request->image))
        {
            $image['image'] = 'image';
        }
        $book = new Book();
        $book->title = $request->title;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;
        $book->save();

        if(!empty($request->image))
        {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $image->move(public_path('uploads/books'),$imageName);
            $book->image = $imageName;
            $book->save();

            // $manager = new ImageManager(Driver::class);
            // $image = $manager->read(public_path('uploads/books/'.$imageName));

            // $image->resize(990);
            // $image->save(public_path('uploads/books/thumb'.$imageName));
        }

        return redirect()->route('book.index')->with('success','Book Added Successfully');


    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}

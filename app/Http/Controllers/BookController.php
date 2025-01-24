<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
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

        if(!empty($request->image))
        {
            $rules['image'] = 'image';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->route('book.edit')->withInput()->withErrors($validator);
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

    // This Function is Used to Edit Book
    public function edit($id)
    {
        $books = Book::findOrFail($id);
        return view('Books.edit',[
            'books'=>$books
        ]);
    }

    // This Method is use for Update Book Detailes
    public function update($id,Request $request)
    {
        $book = Book::findOrFail($id);
        
        

        $rules = [
            'title'=>'required|min:3',
            'author'=>'required|min:3',
            'status'=>'required'
        ];

        if(!empty($request->image))
        {
            $rules['image'] = 'image';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->route('book.edit',$book->id)->withInput()->withErrors($validator);
        }
        //Update Book in the DB
        $book->title = $request->title;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;
        $book->save();

        if(!empty($request->image))
        {
            File::delete(public_path('uploads/books/'.$book->image));
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

        return redirect()->route('book.index')->with('success','Book Update Successfully');
    }

    public function destroy(Request $request)
    {
        $book = Book::find($request->id);
        
        if ($book == null) {
            
            session()->flash('error','Book Not Found');
            return response()->json([
                'status'=>false,
                'message'=> 'Book Not Found'
            ]);
        }
        else
        {
            session()->flash('success','Book Delete Found');
            File::delete(public_path('uploads/book/'.$book->image));
            $book->delete();

            return response()->json([
                'status'=>true,
                'message'=>'Book Delete Successfully from Database'
            ]);
        }
    }
}

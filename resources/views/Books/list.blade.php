@extends('layouts.app')

@section('main')
<div class="container">
    <div class="row my-5">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            @include('layouts.message')
            <div class="card border-0 shadow">
                <div class="card-header  text-white">
                    Books
                </div>
                <div class="card-body pb-0">        
                    <div class="d-flex justify-content-between">
                        <a href="{{route('book.create')}}" class="btn btn-primary">Add Book</a>  
                        <form action="" method="GET">
                            <div class="d-flex">
                                <input type="text" value="{{Request::get('keyword')}}" class="form-control" id="Search" name="keyword" placeholder="Keyword">
                                <button type="submit" class="btn btn-primary ms-2">Submit</button>
                                <a href="{{route('book.index')}}" class="btn btn-secondary ms-2">Clear</a>
                            </div>
                        </form>
                    </div>    
                              
                    <table class="table  table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                            <tbody>
                                @if ($books->isNotEmpty())
                                    @foreach ($books as $book)
                                        <tr>
                                            <td>{{$book->title}}</td>
                                            <td>{{$book->author}}</td>
                                            <td>3.0 (3 Reviews)</td>
                                            <td>
                                                @if ($book->status == 1)
                                                    <button class="btn btn-success">Active</button>
                                                @else
                                                    <button class="btn btn-danger">Block</button>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-success btn-sm"><i class="fa-regular fa-star"></i></a>
                                                <a href="{{route('book.edit',$book->id)}}" class="btn btn-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm" onclick="deleteBook({{$book->id}});"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Books not found
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </thead>
                    </table>  
                    @if ($books->isNotEmpty())
                        {{$books->links()}} 
                    @endif                 
                </div>
                
            </div>                 
        </div>
    </div>       
</div>
@endsection

@section('script')
<script>
    function deleteBook(id)
    {
        if(confirm('Are You Sure Want to Delete This Data ?')){
            $.ajax({
                url:'{{route('book.destroy')}}',
                type:'delete',
                data:{id:id},
                headers:{
                    'X-CSRF-TOKEN' : '{{csrf_token()}}'
                },
                success:function(response){
                    window.location.href = '{{route('book.index')}}';
                }
            });
        }
    }
</script>
@endsection
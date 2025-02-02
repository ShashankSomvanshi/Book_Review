@extends('layouts.app')

@section('main')
<div class="container">
    <div class="row my-5">
        <div class="col-md-3">
            @include('layouts.sidebar')               
        </div>
        <div class="col-md-9">
            
            <div class="card border-0 shadow">
                <div class="card-header  text-white">
                    Edit Reviews
                </div>
                <div class="card-body pb-0">   
                    <form action="{{route('account.myreviews.updateMyReview',$review->id)}}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="review" class="form-label">Title</label>
                                <input type="text" value="{{$review->book->title}}" class="form-control" readonly>
                                @error('review')
                                    <p class="invalid-feedback">{{$message}}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="review" class="form-label">Review</label>
                                <textarea placeholder="Reviews" name="review" class="form-control @error('review') is-invalid @enderror" id="review" cols="10" rows="5">{{old('review',$review->review)}}</textarea>
                                @error('review')
                                    <p class="invalid-feedback">{{$message}}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select name="rating" id="rating" class="form-control">
                                    <option value="1" {{$review->rating == 1 ? 'selected' : ''}}>1</option>
                                    <option value="2" {{$review->rating == 2 ? 'selected' : ''}}>2</option>
                                    <option value="3" {{$review->rating == 3 ? 'selected' : ''}}>3</option>
                                    <option value="4" {{$review->rating == 4 ? 'selected' : ''}}>4</option>
                                    <option value="5" {{$review->rating == 5 ? 'selected' : ''}}>5</option>
                                </select>
                                @error('rating')
                                    <p class="invalid-feedback">{{$message}}</p>
                                @enderror
                            </div>
                              
                            <button class="btn btn-primary mt-2">Update</button>                     
                        </div>
                    </form>             
                </div>
                
            </div>                
        </div>
    </div>       
</div>
@endsection
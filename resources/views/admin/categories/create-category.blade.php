@extends('layouts.admin-layout')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Create New Category</h4>
        </div>
        <div class="card-body">
            {{-- <form>
                <div class="card-body">
                    <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" class="form-control" required="">
                    </div>
                    <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" required="">
                    </div>
                    <div class="form-group">
                    <label>Subject</label>
                    <input type="email" class="form-control">
                    </div>
                    <div class="form-group mb-0">
                    <label>Message</label>
                    <textarea class="form-control" required=""></textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form> --}}
            {!! Form::open(['action' => 'App\Http\Controllers\Admin\CategoriesController@store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-group row">
                    {{ Form::label('title', 'Title',['class' => 'col-form-label text-md-right col-12 col-md-2 col-lg-2']) }}
                    <div class="col-sm-12 col-md-10">
                        {{ Form::text('title','',['class' => 'form-control','placeholde' => 'Category Title']) }}
                    </div>
                </div>

                <div class="form-group row">
                    {{ Form::label('description', 'Description',['class' => 'col-form-label text-md-right col-12 col-md-2 col-lg-2']) }}
                    <div class="col-sm-12 col-md-10">
                        {{ Form::textarea('description','',['class' => 'form-control summernote','placeholde' => 'Category Description']) }}
                     </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label text-md-right col-12 col-md-2 col-lg-2">Image</label>
                    <div class="col-sm-12 col-md-10">
                    <div id="image-preview" class="image-preview">
                        <label for="image-upload" id="image-label">Choose File</label>
                        <input type="file" name="image" id="image-upload" />
                    </div>
                    </div>
                </div>

                <div class="form-group row">
                    {{ Form::label('status', 'Status',['class' => 'col-form-label text-md-right col-12 col-md-2 col-lg-2']) }}
                    <div class="col-sm-12 col-md-10">
                    <select class="form-control selectric" name="status" required>
                        <option value="Published">Publish</option>
                        <option value="Drafted">Draft</option>
                        <option value="Pending">Pending</option>
                    </select>
                     </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label text-md-right col-12 col-md-2 col-lg-2"></label>
                    <div class="col-sm-12 col-md-10">
                        {{ Form::submit('Create Category',['class' => 'btn btn-primary']) }}
                    </div>
                </div>
            {!! Form::close() !!} 
        </div>
      </div>
    </div>
  </div>
@endsection

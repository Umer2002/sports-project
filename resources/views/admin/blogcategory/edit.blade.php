@extends('layouts.admin')

@section('title', 'Edit Blog Category')

@section('content')
<div class="row clearfix">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="header d-flex justify-content-between align-items-center">
                <h2>
                    <i class="material-icons">edit</i> Edit Blog Category
                </h2>
                <ul class="header-dropdown mb-0">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('admin.blogcategory.index') }}">View All</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="body">
                @if ($errors->any())
                    <div class="alert alert-danger rounded">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.blogcategory.update', $blogcategory->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Category Title --}}
                    <h2 class="card-inside-title">Category Title</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="title" class="form-control" value="{{ old('title', $blogcategory->title) }}" placeholder="Enter category title" required>
                        </div>
                        @error('title')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="form-group d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.blogcategory.index') }}" class="btn btn-danger me-2">Cancel</a>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

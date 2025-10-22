@extends('layouts.admin')
@section('title')
    @lang('product.title.delete')
    @parent
@stop

@section('content')
<section class="content-header">
    <h1>@lang('product.title.delete')</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16" data-color="#000"></i>@lang('general.dashboard')</a></li>
        <li><a href="#">@lang('product.title.products')</a></li>
        <li class="active">@lang('product.title.delete')</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4>@lang('product.title.delete')</h4>
                </div>
                <div class="card-body">
                    <p>@lang('product.message.confirm_delete', ['product' => $product->name])</p>
                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">@lang('button.delete')</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">@lang('button.cancel')</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

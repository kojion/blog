@extends('admin.layout')

@section('title', 'コジオニルク - 画像投稿')

@section('content')
    <div id="image" class="row">
    @if(request()->segment(3) === 'edit')
        <form id="delete-image-form" method="POST" action="/admin/images/{{ $image->id }}">
        @csrf
        @method('DELETE')
        </form>
    @endif
        <form method="POST" class="col-12 col-md-5 mt-3" action="/admin/images{{ request()->segment(3) === 'edit' ? "/$image->id" : '' }}" enctype="multipart/form-data">
            <div>
    @csrf
    @if(request()->segment(3) === 'edit')
        @method('PUT')
    @endif
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="name-label">画像名</span>
                </div>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       placeholder="画像名" aria-label="画像名"
                       aria-describedby="name-label" value="{{ old('name') }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="tag-label">タグ</span>
                </div>
                <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple="multiple" style="height: 320px;">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}"
                                @if(in_array($tag->id, old('tags') ?? [])) selected="selected" @endif>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                @error('tags')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
    @if(request()->segment(3) === 'create')
            <div class="input-group mb-3">
                <div class="custom-file">
                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file"/>
                </div>
            </div>
        @error('file')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    @endif
            <div class="input-group mb-3">
                <input type="submit" id="save-image" class="btn btn-primary ms-auto" value="保存"/>
    @if(request()->segment(3) === 'edit')
                <input type="button" id="delete-image" class="btn btn-danger" value="削除"/>
    @endif
            </div>
        </form>
    @isset($image)
        <div class="post col-12 col-md-7 mt-3">
            <img class="rounded img-fluid" src="/storage/image/{{ $image->image }}"/>
        </div>
    @endisset
    </div>
@endsection

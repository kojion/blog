@extends('admin.layout')

@section('title', 'コジオニルク - Wiki 投稿')

@section('content')
    <div class="row">
    @if(request()->segment(3) === 'edit')
        <form id="delete-wiki-form" method="POST" action="/admin/wikis/{{ request()->segment(4) }}">
            @csrf
            @method('DELETE')
        </form>
    @endif
        <form method="POST" id="wiki-form" class="col-12 col-md-9 mt-3" action="/{{ request()->path() }}">
            <div class="input-group mb-3">
                @csrf
                @if(request()->segment(3) === 'edit')
                    @method('PUT')
                @endif
                <textarea id="markdown" name="markdown" class="form-control @error('markdown') is-invalid @enderror" rows="16">{{ old('markdown') }}</textarea>
    @error('markdown')
                <div class="invalid-feedback"></div>
    @enderror
            </div>
            <div class="input-group mb-3">
                <input type="submit" class="btn btn-secondary ms-auto" value="プレビュー"/>
                <input id="store-wiki" type="submit" class="btn btn-info" value="保存"/>
                @if(request()->segment(3) === 'edit' && request()->segment(4) !== '1')
                    <input id="delete-wiki" type="button" class="btn btn-danger" value="削除"/>
                @endif
            </div>
            <div class="wiki">
                {!! $wiki->html ?? '' !!}
            </div>
        </form>
        <div class="d-none d-md-block col-3 py-3">
            <div id="sidebar" class="card p-2">
                <div id="edit-sidebar">
                    <a href="/admin/wikis/edit/1">編集</a>
                </div>
                {!! $sidebar->html !!}
            </div>
        </div>
    </div>
@endsection

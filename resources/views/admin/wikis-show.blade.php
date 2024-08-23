@extends('admin.layout')

@section('title', 'コジオニルク - Wiki')

@section('content')
    <div class="row">
        <div class="col-12 col-md-9 py-3">
            <div class="wiki" class="px-3">
                <div id="edit-wiki">
                    <a href="/admin/wikis/edit/{{ $wiki->id }}">編集</a>
                </div>
                {!! $wiki->html !!}
            </div>
        </div>
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

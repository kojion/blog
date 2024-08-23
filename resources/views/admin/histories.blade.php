@extends('admin.layout')

@section('title', 'コジオニルク - Wiki 履歴')

@section('content')
    <div class="row">
        <div class="col-12 col-md-9 py-3">
            <div class="row mb-4 mx-2 alert alert-info">
                <div class="col-6">
                    <b>Rev. {{ $history->rev_no }}</b> ({{ $history->created_at }})
                </div>
                <div class="col-6 text-right">
    @if($history->prev)
                    <a href="/admin/histories/{{ $history->prev->id }}" class="btn btn-sm btn-primary">&lt; {{ $history->prev->created_at }}</a>
    @endif
    @if($history->next)
                    <a href="/admin/histories/{{ $history->next->id }}" class="btn btn-sm btn-primary">{{ $history->next->created_at }} &gt;</a>
    @endif
                </div>
            </div>
            <div class="wiki" class="px-3">
                {!! $history->html !!}
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

@extends('admin.layout')

@section('title', 'コジオニルク - 予定登録')

@section('content')
    <div class="row">
    @if(request()->segment(3) === 'edit')
        <form id="delete-plan-form" method="POST" action="/admin/plans/{{ request()->segment(4) }}">
            @csrf
            @method('DELETE')
        </form>
    @endif
        <form method="POST" id="plan-form" class="col-12 col-md-9 mt-3" action="/{{ request()->path() }}">
            <div class="input-group mb-3">
                @csrf
                @if(request()->segment(3) === 'edit')
                    @method('PUT')
                @endif
                <div class="input-group-prepend">
                    <span class="input-group-text" id="date-label">日付</span>
                </div>
                <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror"
                       placeholder="日付" aria-label="日付"
                       aria-describedby="date-label" value="{{ old('date') }}">
                @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="date-label">時間</span>
                </div>
                <input type="time" id="time" name="time" class="form-control @error('time') is-invalid @enderror"
                       placeholder="時間" aria-label="時間"
                       aria-describedby="time-label" value="{{ old('time') }}">
                @error('time')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="name-label">概要</span>
                </div>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                       placeholder="概要" aria-label="概要"
                       aria-describedby="name-label" value="{{ old('name') }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="input-group mb-3">
                <input id="store-plan" type="submit" class="btn btn-info ml-auto" value="保存"/>
                @if(request()->segment(3) === 'edit')
                    <input id="delete-plan" type="button" class="btn btn-danger ml-2" value="削除"/>
                @endif
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

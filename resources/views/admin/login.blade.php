@extends('blog.layout')

@section('title', 'コジオニルク - ログイン')

@section('twitter')
@endsection

@section('navbar')
@endsection

@section('content')
    <form class="px-5 py-4 my-0" method="post">
        @if($error)
        <div class="alert alert-danger" role="alert">
            {{ $error }}
        </div>
        @endif
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">メール</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
    </form>
@endsection

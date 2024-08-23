<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <a href="/admin" class="navbar-brand">コジオニルク</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-supported-content" aria-controls="navbar-supported-content" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-supported-content">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/posts/create' ? ' active' : '' }}" href="/admin/posts/create">記事投稿</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/images/create' ? ' active' : '' }}" href="/admin/images/create">画像投稿</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/plans/create' ? ' active' : '' }}" href="/admin/plans/create">予定作成</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/tasks' ? ' active' : '' }}" href="/admin/tasks">ToDo 一覧</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/wikis/create' ? ' active' : '' }}" href="/admin/wikis/create">Wiki 作成</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->path() === 'admin/tags' ? ' active' : '' }}" href="/admin/tags">タグ管理</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Blog</a>
                    </li>
                </ul>
                <form class="form-inline ms-2" method="post" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary my-2 my-sm-0">ログアウト</button>
                </form>
            </div>
        </nav>
        <div id="app" class="container-fluid" data-token="{{ \Auth::user()->api_token ?? null }}">
@if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3 mx-2" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
@endif
@yield('content')
        </div>
    </body>
</html>

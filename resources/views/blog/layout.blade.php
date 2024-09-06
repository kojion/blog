<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        @vite(['resources/sass/blog.scss', 'resources/js/blog.js'])
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-106568102-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-106568102-1');
        </script>
        @yield('twitter')
    </head>
    <body>
        <div id="app" class="container" data-token="{{ \Auth::user()->api_token ?? null }}">
            <nav class="navbar navbar-expand-lg px-2 px-md-4">
                <a href="/" class="navbar-brand">コジオニルク</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-supported-content" aria-controls="navbar-supported-content" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar-supported-content">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link{{ in_array(request()->path(), ['posts/list', 'images']) ? '' : ' active' }}" href="/">日記</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link{{ request()->path() === 'posts/list' ? ' active' : '' }}" href="/posts/list">タイトル一覧</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link{{ request()->path() === 'images' ? ' active' : '' }}" href="/images">ギャラリー</a>
                        </li>
    @if(\Illuminate\Support\Facades\Auth::check())
                            <li class="nav-item">
                                <a class="nav-link" href="/admin">管理</a>
                            </li>
    @endif
                    </ul>
@yield('navbar')
                </div>
            </nav>
            <div id="content">
    @if(session('success'))
                <div class="pt-3 px-4">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
    @endif
@yield('content')
            </div>
            <footer class="text-end container">これは個人の日記帳です。 Copyright © 2016 - 2024 Hideyuki Kojima.</footer>
        </div>
    </body>
</html>

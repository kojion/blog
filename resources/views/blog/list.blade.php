@extends('blog.layout')

@section('title', 'コジオニルク - タイトル一覧')

@section('twitter')
    <meta name="twitter:title" content="コジオニルク - タイトル一覧"/>
    <meta name="twitter:site" content="@kojionilk"/>
    <meta name="twitter:description" content="日記帳です。"/>
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:image" content="http://www.kojion.com/twitter.png"/>
@endsection

@section('navbar')
    <ul class="navbar-nav navbar-right">
        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#tag-list">
                {{ is_null($tag) ? 'すべて' : $tag->name }}
            </a>
        </li>
    </ul>
    <div class="modal fade" id="tag-list" tabindex="-1" role="dialog" aria-labelledby="tag-list-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tag-list-title">タグ一覧</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-3">
                        <a href="/posts/list">すべて</a>
                    </div>
    @foreach($tags as $x)
                    <div class="col-3">
                        <a href="/posts/list?tag_id={{ $x->id }}">
                            {{ $x->name }}
                            <span class="badge badge-pill badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$x->color] }}">
                                {{ $x->posts_count }}
                            </span>
                        </a>
                    </div>
    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if($posts->total() > 0)
    <div id="message" class="pt-3 text-center">
        記事 {{ $posts->total() }} 件中 {{ $posts->firstItem() }} ～ {{ $posts->lastItem() }} 件を表示しています。
    </div>
    @endif
    @if($posts->lastPage() > 1)
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item {{ $posts->currentPage() === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="/posts/list?page=1{{ is_null($tag) ? '' : "&tag_id=$tag->id" }}">&laquo;</a>
            </li>
            @foreach($pages as $page)
                <li class="page-item {{ $page === $posts->currentPage() ? 'active' : ''}}">
                    <a class="page-link" href="/posts/list?page={{ $page }}{{ is_null($tag) ? '' : "&tag_id=$tag->id" }}">{{ $page }}</a>
                </li>
            @endforeach
            <li class="page-item {{ $posts->currentPage() === $posts->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="/posts/list?page={{ $posts->lastPage() }}{{ is_null($tag) ? '' : "&tag_id=$tag->id" }}">&raquo;</a>
            </li>
        </ul>
    </nav>
    @endif
    <div class="px-4 py-4">
        <table id="posts-list" class="mb-0">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell text-center">ID</th>
                    <th class="text-center">日付</th>
                    <th class="text-center">タイトル</th>
                    <th class="text-center">タグ</th>
                </tr>
            </thead>
            <tbody>
    @foreach($posts as $post)
                <tr>
                    <td class="text-center d-none d-md-table-cell">
                        <a href="/posts/{{ $post->id }}">{{ $post->id }}</a>
                    </td>
                    <td class="text-center">
                        <a href="/posts/{{ $post->id }}">{{ $post->year }}/{{ $post->month }}/{{ $post->day }}</a>
                    </td>
                    <td class="name">
                        <a href="/posts/{{ $post->id }}">{{ $post->name }}</a>
                    </td>
                    <td class="text-center">
        @foreach($post->tags as $tag)
                        <a href="/posts/list?tag_id={{ $tag->id }}">
                            <span class="badge badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$tag->color] }}">
                                {{ $tag->name }}
                            </span>
                        </a>
        @endforeach
                    </td>
                </tr>
    @endforeach
            </tbody>
        </table>
    </div>
@endsection

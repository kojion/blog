@extends('blog.layout')

@section('title', 'コジオニルク - ギャラリー')

@section('twitter')
    <meta name="twitter:title" content="コジオニルク - ギャラリー"/>
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
                        <a href="/images">すべて</a>
                    </div>
    @foreach($tags as $x)
                    <div class="col-3">
                        <a href="/images?tag_id={{ $x->id }}">
                            {{ $x->name }}
                            <span class="badge badge-pill badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$x->color] }}">
                                {{ $x->images_count }}
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
    @if($images->total() > 0)
    <div id="message" class="text-center pt-4">
        {{ $images->total() }} 件中 {{ $images->firstItem() }} ～ {{ $images->lastItem() }} 件を表示しています。
    </div>
    @endif
    @if($images->lastPage() > 1)
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center">
            @foreach(range(1, $images->lastPage()) as $page)
                <li class="page-item {{ $page === $images->currentPage() ? 'active' : ''}}">
                    <a class="page-link" href="/images?page={{ $page }}{{ is_null($tag) ? '' : "&tag_id=$tag->id" }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
    @endif
    <div class="row px-4">
    @foreach($images as $image)
        <div class="gallery col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="image my-5 my-md-3 my-lg-1">
                <a href="/storage/image/{{ $image->image }}">
                    <img src="/storage/thumbnail/{{ $image->image }}" class="rounded img-fluid"/>
                </a>
            </div>
        @if(\Illuminate\Support\Facades\Auth::check())
            <div class="markdown">
                <b>![{{ $image->name }}]({{ $image->image }})</b><br/>
                {{ $image->width }}x{{ $image->height }} ({{ number_format($image->size) }} byte)
            </div>
        @else
            <div class="date">{{ $image->created_at }}</div>
        @endif
            <div class="name">{{ $image->name }}</div>
            <div class="tags">
        @foreach($image->tags as $tag)
                <a href="/images?tag_id={{ $tag->id }}">
                    <span class="badge badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$tag->color] }}">
                            {{ $tag->name }}
                    </span>
                </a>
        @endforeach
        @if(\Illuminate\Support\Facades\Auth::check())
                <a class="btn btn-primary btn-sm" href="/admin/images/edit/{{ $image->id }}">編集</a>
        @endif
            </div>
        </div>
    @endforeach
    </div>
@endsection

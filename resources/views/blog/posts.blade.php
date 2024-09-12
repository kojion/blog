@extends('blog.layout')

@section('title', 'コジオニルク' . (is_numeric(request()->segment(2)) ? ' - ' . $posts[0]->name : ""))

@section('twitter')
    <meta name="twitter:title" content="{{ 'コジオニルク' . (is_numeric(request()->segment(2)) ? ' - ' . $posts[0]->name : "") }}"/>
    <meta name="twitter:site" content="@kojionilk"/>
    <meta name="twitter:description" content="{{ is_numeric(request()->segment(2)) ? mb_substr($posts[0]->markdown, 0, 100): "日記帳です。" }}"/>
    <meta name="twitter:card" content="{{ count($posts) > 0 && $posts[0]->first_image_url ? 'summary_large_image' : 'summary' }}"/>
    <meta name="twitter:image" content="{{ count($posts) > 0 && $posts[0]->first_image_url ? $posts[0]->first_image_url : 'http://www.kojion.com/twitter.png' }}"/>
@endsection

@section('navbar')
    <form class="d-flex my-2 my-lg-0" method="get" action="/posts">
        <input type="search" class="form-control me-sm-2" name="query" placeholder="検索..." aria-label="検索...">
        <button type="submit" class="btn btn-light my-2 my-sm-0">Search</button>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#month-list">
    @if(isset($year, $month))
                <span>{{ $year }} 年 {{ $month }} 月</span>
    @elseif(request()->has('tag_id'))
                <span>{{ $tagName }}</span>
    @endif
            </a>
        </li>
    </ul>
    <div class="modal fade" id="month-list" tabindex="-1" role="dialog" aria-labelledby="month-list-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="month-list-title">記事</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <h2 class="col-12">タグ別</h2>
    @foreach($tags as $x)
                    <div class="col-lg-3 col-md-4 col-6 text-end">
                        <a href="/posts?tag_id={{ $x->id }}">
                            {{ $x->name }}
                            <span class="badge badge-pill badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$x->color] }}">
                            {{ $x->posts_count }}
                        </span>
                        </a>
                    </div>
    @endforeach
                    <hr class="my-4"/>
    @foreach($yearMonths as $yearMonth)
        @if($loop->index === 0 || $yearMonth->year !== $yearMonths[$loop->index - 1]->year)
                    <h2 class="col-12">{{ $yearMonth->year }} 年</h2>
        @endif
                    <div class="col-lg-3 col-md-4 col-6 text-end">
                        <a href="posts?year={{ $yearMonth->year }}&month={{ $yearMonth->month }}">
                            {{ $yearMonth->year }} 年 {{ $yearMonth->month }} 月
                            <span class="badge badge-pill badge-primary">{{ $yearMonth->count }}</span>
                        </a>
                    </div>
    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @isset($firstDate)
    <div class="row">
        <div id="calendar" class="col mx-4 mb-1">
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item @if($firstDate->year === $year && $firstDate->month === $month) disabled @endif">
                        <a class="page-link" href="/posts?year={{ $firstDate->year }}&month={{ $firstDate->month }}">
                            {{ $firstDate->year === $year && $firstDate->month === $month ? '---' : '«'}}
                        </a>
                    </li>
                    <li class="page-item @if($firstDate->year === $year && $firstDate->month === $month) disabled @endif">
                        <a class="page-link" href="/posts?year={{ $prevYearMonth->year }}&month={{ $prevYearMonth->month }}">
                            {{ $firstDate->year === $year && $firstDate->month === $month ? '---' : "‹ $prevYearMonth->month 月"}}
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#" data-bs-toggle="modal" data-bs-target="#month-list">
                            {{ $year }} 年 {{ $month }} 月
                        </a>
                    </li>
                    <li class="page-item @if($yearMonths[0]->year === $year && $yearMonths[0]->month === $month) disabled @endif">
                        <a class="page-link" href="/posts?year={{ $nextYearMonth->year }}&month={{ $nextYearMonth->month }}">
                            {{ $yearMonths[0]->year === $year && $yearMonths[0]->month === $month ? '---' : "$nextYearMonth->month 月 ›" }}
                        </a>
                    </li>
                    <li class="page-item @if($yearMonths[0]->year === $year && $yearMonths[0]->month === $month) disabled @endif">
                        <a class="page-link" href="/posts?year={{ $yearMonths[0]->year }}&month={{ $yearMonths[0]->month }}">
                            {{ $yearMonths[0]->year === $year && $yearMonths[0]->month === $month ? '---' : '»' }}
                        </a>
                    </li>
                </ul>
            </nav>
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
        @foreach(['日', '月', '火', '水', '木', '金', '土'] as $x)
                        <th class="text-center">{{ $x }}</th>
        @endforeach
                    </tr>
                </thead>
                <tbody>
        @foreach($calendarDates as $chunk)
                    <tr>
            @foreach($chunk as $date)
                        <td class="@if($date->month != $month) grey @elseif(in_array($date->day, $existsDates)) exists @endif">
                            {{ $date->day }}
                        </td>
            @endforeach
                    </tr>
        @endforeach
                </tbody>
            </table>
        </div>
        <div id="info" class="col d-none d-md-block p-2 mt-4 me-md-4 me-xl-0">
            <p>
                ここは Web や Android アプリのプログラマでありチェスやバイク、株式投資を趣味とするコジオンこと Hideyuki Kojima の日記です。
                毎日何かしら欠かさず書いています。
                この Blog の他に
                <a href="https://qiita.com/kojionilk">Qiita</a> にもいくつか技術系の記事を投稿しています。
                <a href="https://youtube.com/channel/UCVqCcOKMgrti7Y5v20GX0oA">YouTube のチェス実況チャンネル</a>
                に毎日 <a href="https://lichess.org/">lichess</a> か <a href="https://chess.com/">Chess.com</a> の 10 分レート戦の実況動画を投稿しています。
                連絡はメールでお願いします。kojionilk あっとまーく gmail どっと com です。
            </p>
        </div>
        <div class="col d-none d-xl-block py-2 me-xl-4">
            <div class="pt-2 text-center">
                <img src="logo.png" width="30%" height="30%"/>
            </div>
            <div class="text-center small fw-bold">
                <a href="https://youtube.com/channel/UCVqCcOKMgrti7Y5v20GX0oA">コジオン: チェス実況 (YouTube)</a>
            </div>
            <div class="text-center small fw-bold">
                <a href="https://kojion.github.io/chess">チェスサイト (kojion.github.io)</a>
            </div>
            <div class="text-center small fw-bold">
                <a href="https://x.com/zbxah">X (旧 Twitter)</a>
            </div>
            <div class="alert alert-secondary mx-2 mt-2" role="alert">
                <div>{{ $firstDate->year }}/{{ $firstDate->month }}/{{ $firstDate->day }} から日記を書いています。</div>
                <div>今まで <strong>{{ $count }}</strong> 件の日記を書きました。</div>
            </div>
        </div>
    </div>
    @endisset
    @if(request()->has('query') || request()->has('tag_id'))
    <div id="message" class="pt-4 text-center">
        @if(request()->has('query'))
            検索語句「{{ request()->query('query') }}」を含む
        @else
            タグ「{{ $tagName }}」の
        @endif
        @if($posts->total() > 0)
            記事 {{ $posts->total() }} 件中 {{ $posts->firstItem() }} ～ {{ $posts->lastItem() }} 件を表示しています。
        @else
            記事が存在しません。
        @endif
    </div>
        @if($posts->lastPage() > 1)
    <nav class="mt-3">
            @php
            $query = request()->has('query') ? 'query=' . request()->query('query') : 'tag_id=' . request()->query('tag_id');
            @endphp
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item {{ $posts->currentPage() === 1 ? 'disabled' : '' }}">
                <a class="page-link" href="posts?{{ $query }}&page=1">&laquo;</a>
            </li>
            @foreach($pages as $page)
            <li class="page-item {{ $page === $posts->currentPage() ? 'active' : ''}}">
                <a class="page-link" href="posts?{{ $query }}&page={{ $page }}">{{ $page }}</a>
            </li>
            @endforeach
            <li class="page-item {{ $posts->currentPage() === $posts->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" href="posts?{{ $query }}&page={{ $posts->lastPage() }}">&raquo;</a>
            </li>
        </ul>
    </nav>
        @endif
    @endif

    @foreach($posts as $post)
    <div class="post p-1 p-sm-2 p-xl-4 {{ $loop->first ? '' : 'mt-4' }}">
        <a name="{{ $post->day }}"></a>
        <div class="post-title row mx-1 mx-md-2 py-1">
            <div class="title col">
        @if(isset($post->date))
                {{ $post->date }}&nbsp;&nbsp;&nbsp;{{ $post->name }}
        @else
                <a href="/posts/{{ $post->id }}">{{ $post->year }}/{{ $post->month }}/{{ $post->day }}&nbsp;&nbsp;&nbsp;{{ $post->name }}</a>
        @endif
            </div>
            <div class="post-tags col-md-auto d-none d-md-block text-end">
        @foreach($post->tags as $tag)
                <a href="/posts?tag_id={{ $tag ? $tag->id : ''}}">
                    <span class="badge badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$tag->color] }}">{{ $tag->name }}</span>
                </a>
        @endforeach
            </div>
        </div>
        @if(strlen($post->deleted_at))
        <div class="alert alert-warning mt-3 mx-4">この記事は非公開です。</div>
        @endif
        <div class="post-content px-1 px-sm-2 px-xl-4">{!! $post->html !!}</div>
        <div class="add-comment text-end px-4">
            @if (\Illuminate\Support\Facades\Auth::check())
            <a class="btn btn-primary btn-sm me-2" href="/admin/posts/edit/{{ $post->id }}">記事編集</a>
            @endif
        </div>
    </div>
    @endforeach
@endsection

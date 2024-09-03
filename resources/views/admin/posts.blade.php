@extends('admin.layout')

@section('title', 'コジオニルク - 記事投稿')

@section('content')
    <div id="post" class="row">
        <form method="POST" class="col-12 col-xl-5 mt-3" action="/{{ request()->path() }}">
            <div class="row">
                <div class="col-9">
    @csrf
    @if(request()->segment(3) === 'edit')
        @method('PUT')
    @endif
                    <input type="hidden" name="enabled" id="post-enabled"/>
                    <div class="input-group mb-3">
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
                            <span class="input-group-text" id="name-label">記事名</span>
                        </div>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="記事名" aria-label="記事名"
                            aria-describedby="name-label" value="{{ old('name') }}">
    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
                    </div>
                    <div class="row">
    @foreach($images as $image)
                        <div class="recent-image col-3">
                            <div class="image">
                                <img src="/storage/thumbnail/{{ $image->image }}" class="rounded img-fluid"/>
                            </div>
                            <div class="markdown my-1">
                                ![{{ $image->name }}]({{ $image->image }})
                            </div>
                        </div>
    @endforeach
                    </div>
                </div>
                <div class="mb-3 col-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="tag-label">タグ</span>
                    </div>
                    <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple="multiple">
    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}"
                            @if(in_array($tag->id, old('tags') ?? [])) selected="selected" @endif>
                            {{ $tag->name }}
                        </option>
    @endforeach
                    </select>
    @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
                </div>
            </div>
            <div class="input-group mb-3">
                <textarea id="markdown" name="markdown" class="form-control @error('markdown') is-invalid @enderror" rows="16">{{ old('markdown') }}</textarea>
    @error('markdown')
                <div class="invalid-feedback">{{ $message }}</div>
    @enderror
            </div>
            <div class="input-group mb-3">
                <input type="submit" class="btn btn-secondary ms-auto" value="プレビュー"/>
                <input id="post-disabled-store" type="button" class="btn btn-secondary" value="非公開"/>
                <input id="post-enabled-store" type="button" class="btn btn-info" value="公開"/>
            </div>
        </form>
        <div class="post col-12 col-xl-7 mt-3">
    @isset($post)
            <div class="post-title row ml-1 ml-md-2 mr-1 mr-md-2 py-1">
                <div class="title col">@if($post->year > 0){{ $post->year }}/{{ $post->month }}/{{ $post->day }}@endif&nbsp;&nbsp;&nbsp;{{ $post->name }}</div>
                <div class="post-tags col-md-auto text-right">
                    @foreach($post->tags as $tag)
                        <a href="/posts/list?tag_id={{ $tag ? $tag->id : ''}}">
                            <span class="badge badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$tag->color] }}">{{ $tag->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @if(strlen($post->deleted_at))
                <div class="alert alert-warning mt-3 mx-4">この記事は非公開です。</div>
            @endif
            <div class="post-content px-1 px-sm-2 px-md-4">{!! $post->html !!}</div>
        </div>
    @endisset
    </div>
@endsection

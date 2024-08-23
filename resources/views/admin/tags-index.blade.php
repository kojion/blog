@extends('admin.layout')

@section('title', 'コジオニルク - タグ管理')

@section('content')
    <div class="row">
        <div class="col-12 col-md-9">
            <table class="table table-sm table-bordered my-4">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell text-center">ID</th>
                        <th class="text-center">名前</th>
                        <th class="text-center">並び順</th>
                        <th class="text-center">記事件数</th>
                        <th class="text-center">画像件数</th>
                        <th class="text-center">作成日時</th>
                        <th class="text-center">更新日時</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($tags as $tag)
                    <tr>
                        <td class="px-2 text-center">{{ $tag->id }}</td>
                        <td class="px-2 text-center">
                            <span class="badge badge-{{ \App\Models\Tag::BOOTSTRAP_CLASSES[$tag->color] }}">
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="px-2 text-right">{{ $tag->rank }}</td>
                        <td class="px-2 text-right">{{ $tag->posts_count }}</td>
                        <td class="px-2 text-right">{{ $tag->images_count }}</td>
                        <td class="px-2 text-right">{{ $tag->created_at }}</td>
                        <td class="px-2 text-right">{{ $tag->updated_at }}</td>
                    </tr>
    @endforeach
                </tbody>
            </table>
        </div>
        <div class="post d-none d-md-block col-3 py-3">
            <div id="sidebar" class="card p-2">
                <div id="edit-sidebar">
                    <a href="/admin/wikis/edit/1">編集</a>
                </div>
                {!! $sidebar->html !!}
            </div>
        </div>
    </div>
@endsection

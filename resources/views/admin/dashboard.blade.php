@extends('admin.layout')

@section('title', 'コジオニルク - ダッシュボード')

@section('content')
    <div class="row">
        <div class="col-5 d-none d-sm-block">
            <div id="calendar-header" class="mt-3 text-center">
                <a class="me-3 btn btn-sm btn-primary" href="/admin?year={{ $month === 1 ? $year - 1 : $year }}&month={{ $month === 1 ? 12 : $month - 1 }}">前月</a>
                {{ $year }} 年 {{ $month }} 月
                <a class="ms-3 btn btn-sm btn-primary" href="/admin?year={{ $month === 12 ? $year + 1 : $year }}&month={{ $month === 12 ? 1 : $month + 1 }}">次月</a>
            </div>
            <table id="calendar" class="mt-2">
                <thead>
                <tr>
                    @foreach(['日', '月', '火', '水', '木', '金', '土'] as $x)
                        <th class="text-center py-1">{{ $x }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($calendarDates as $chunk)
                    <tr>
                        @foreach($chunk as $date)
                            <td class="{{ $date['class'] }}" data-date="{{ $date['date']->format('Y-m-d') }}">
                                <div class="date py-1 pe-2 text-end" data-date="{{ $date['date']->format('Y-m-d') }}">
                                    {{ $date['date']->day }}
                            @if($date['holiday'] !== null)
                                    <div>{{ $date['holiday'] }}</div>
                            @endif
                                </div>
                            @if(count($date['plans']))
                                <div class="plans">
                                @foreach($date['plans'] as $plan)
                                    <div>
                                        <a href="/admin/plans/edit/{{ $plan->id }}">
                                    @if($plan->time)
                                        <b>{{ $plan->time }}</b>
                                    @endif
                                        {{ $plan->name }}
                                        </a>
                                    </div>
                                @endforeach
                                </div>
                            @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div id="task-header" class="text-center mt-4">
                ToDo
            </div>
            <div id="task-header2" class="text-end">
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#task-modal">追加</button>
            </div>
            @foreach($taskGroups as $taskGroup)
                @if($taskGroup->tasksWithoutDoneAt->count())
            <div class="task-group mx-2 my-3">{{ $taskGroup->name }}</div>
                    @foreach($taskGroup->tasksWithoutDoneAt as $task)
                <form action="/admin/tasks/{{ $task->id }}" method="POST">
                    <div class="form-group ms-5 mb-0">
                        @csrf
                        @method('PUT')
                        <input type="checkbox" class="check-task form-check-input">
                        <input type="hidden" name="task_group_id" value="{{ $task->taskGroup->id }}"/>
                        <input type="hidden" name="name" value="{{ $task->name }}"/>
                        <input type="hidden" name="done_at" value="now"/>
                        {{ $task->name }}
                    </div>
                </form>
                    @endforeach
                @endif
            @endforeach
        </div>
        <div class="col-4 pt-3 d-none d-sm-block">
            @if($days <= 0)
                <div class="alert alert-success mx-2" role="alert">
                    本日までの記事を投稿済です。
                </div>
            @elseif($days === 1)
                <div class="alert alert-info mx-2" role="alert">
                    本日の記事を投稿しておりません。
                </div>
            @else
                <div class="alert alert-{{ $days === 2 ? 'warning' : 'danger' }} mx-2" role="alert">
                    {{ $days === 2 ? '昨日' : ($days - 1) . " 日前" }}までの記事を投稿しておりません！
                </div>
            @endif
            <div class="mt-2 mb-1 font-weight-bold text-center">記事 (更新日時順)</div>
            <table class="table table-sm table-bordered">
                <tr class="thead-dark">
                    <th class="text-center">ID</th>
                    <th class="text-center">年月日</th>
                    <th class="text-center">タイトル</th>
                    <th class="text-center">更新日時</th>
                </tr>
                @foreach($posts as $post)
                    <tr>
                        <td class="px-2 text-center"><a href="/posts/{{ $post->id }}">{{ $post->id }}</a></td>
                        <td class="px-2 text-center">{{ $post->year }}/{{ $post->month }}/{{ $post->day }}</td>
                        <td class="px-2"><a href="/posts/{{ $post->id }}">{{ $post->name }}</a></td>
                        <td class="px-2 text-end">{{ $post->updated_at->format('m-d H:i') }}</td>
                    </tr>
                @endforeach
            </table>
            <div class="mb-1 font-weight-bold text-center">バックアップ</div>
            <table class="table table-sm table-bordered">
                <tr class="thead-dark">
                    <th class="text-center">日時</th>
                    <th class="text-center">サイズ</th>
                </tr>
    @foreach($backups as $backup)
                <tr>
                    <td class="px-2">{{ $backup['last_modified']->format('Y-m-d H:i') }}</td>
                    <td class="px-2 text-right font-weight-bold">{{ number_format($backup['size'] / 1073741824, 2) }} GB</td>
                </tr>
    @endforeach
            </table>

            <div class="font-weight-bold text-center mt-2 mb-1">Wiki 日時順</div>
            <table class="table table-sm table-bordered">
                <tr class="thead-dark">
                    <th class="text-center">タイトル</th>
                    <th class="text-center">最終更新</th>
                    <th class="text-center">履歴数</th>
                </tr>
    @foreach($wikis as $wiki)
                <tr>
                    <td class="px-2"><a href="/admin/wikis/{{ $wiki->id }}">{{ $wiki->title }}</a></td>
                    <td class="px-2">{{ $wiki->updated_at->format('Y-m-d H:i') }}</td>
                    <td class="px-2 text-right"><a href="/admin/histories/{{ $wiki->last_history->id }}">{{ $wiki->histories_count }}</a></td>
                </tr>
    @endforeach
            </table>
        </div>
        <div class="col-12 col-sm-3 pt-3">
            <div id="sidebar" class="card p-2">
                <div id="edit-sidebar">
                    <a href="/admin/wikis/edit/1">編集</a>
                </div>
                {!! $sidebar->html !!}
            </div>
        </div>
    </div>
    <div class="modal fade" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="task-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="task-modal-label">ToDo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/admin/tasks" method="POST">
                        <div class="form-group">
                            {{ csrf_field() }}
                            <select class="custom-select" name="task_group_id">
    @foreach($taskGroups as $taskGroup)
                                <option value="{{ $taskGroup->id }}">{{ $taskGroup->name }}</option>
    @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="task-modal-name">タスク</label>
                            <input type="text" name="name" class="form-control" id="task-modal-name" placeholder="タスク">
                        </div>
                        <div class="form-group text-right">
                            <input type="submit" class="btn btn-primary" value="登録"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

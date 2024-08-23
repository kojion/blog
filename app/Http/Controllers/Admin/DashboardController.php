<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Post;
use App\Models\TaskGroup;
use App\Models\Wiki;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * ダッシュボードを表示する.
     *
     * @param Request $request リクエスト
     * @return View ビュー
     */
    public function __invoke(Request $request): View
    {
        // カレンダーの指し示す年月を取得 (GET パラメータが無い場合は現在年月)
        $year = (int) $request->query('year', Carbon::now()->year);
        $month = (int) $request->query('month', Carbon::now()->month);

        // カレンダーの指し示す年月の予定を取得 (カレンダーの描画範囲が欲しいが計算が面倒なので前後 3 ヶ月分取得)
        $between = [Carbon::createFromDate($year, $month - 1, 1), Carbon::createFromDate($year, $month + 2, 0)];
        $plans = Plan::whereBetween('date', $between)->orderBy('time')->get();

        // カレンダーに描画する日付を 7 日ごとにチャンク化して生成する
        $firstDate = Carbon::createFromDate($year, $month, 1);
        $date = $firstDate->subDays($firstDate->dayOfWeek);
        $calendarDates = [];
        do {
            $f = function (int $x) use ($date, $year, $month, $plans): array {
                $carbon = Carbon::createFromDate($date->year, $date->month, $date->day + $x);
                $holiday = $this->getHolidayName($carbon) ?? ($this->isExtra($carbon) ? '振替休日' : '');
                $now = Carbon::now();
                if ($now->year === $carbon->year && $now->month === $carbon->month && $now->day === $carbon->day) {
                    $class = 'today';
                } elseif ($carbon->month !== $month) {
                    $class = 'grey';
                } elseif (strlen($holiday) || $carbon->dayOfWeek === Carbon::SUNDAY) {
                    $class = 'sunday';
                } elseif ($carbon->dayOfWeek === Carbon::SATURDAY) {
                    $class = 'saturday';
                } else {
                    $class = '';
                }
                $localPlans = $plans->filter(fn(Plan $x) => $x->date === $carbon->format('Y-m-d'));
                return ['date' => $carbon, 'class' => $class, 'holiday' => $holiday, 'plans' => $localPlans];
            };
            $calendarDates[] = collect(range(0, 6))->map($f);
            $date->addDays(7);
        } while (intval($month) === $date->month);

        // タスクグループを取得
        $taskGroups = TaskGroup::query()->orderBy('rank')->get();

        // 最後の日付を取得
        $lastPost = Post::orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('day')
            ->first(['year', 'month', 'day']);
        $lastPostDate = Carbon::createFromDate($lastPost->year, $lastPost->month, $lastPost->day);

        // 本日との日付の差を計算
        $days = $lastPostDate->daysUntil(Carbon::now())->count() - 1;

        // 最近更新した投稿一覧を取得
        $posts = Post::withTrashed()->orderByDesc('updated_at')->limit(5)->get();

        // バックアップディレクトリの情報を取得
        $backups = collect(Storage::files('backup'))
            ->sort(fn($x, $y) => Storage::lastModified($x) - Storage::lastModified($y) < 0)
            ->map(fn($x) => [
                'name' => basename($x),
                'last_modified' => Carbon::createFromTimestamp(Storage::lastModified($x)),
                'size' => Storage::size($x)
            ])->toArray();

        // Wiki 一覧を取得
        $wikis = Wiki::withCount('histories')->orderByDesc('updated_at')->get();

        // サイドバー Wiki のデータを取得
        $sidebar = Wiki::whereId(1)->firstOrFail();

        return view('admin.dashboard', compact('year', 'month', 'calendarDates', 'taskGroups', 'days', 'posts', 'backups', 'wikis', 'sidebar'));
    }

    /**
     * その日が祝日であれば祝日名を返す.
     * 祝日でなければ null を返す.
     *
     * @param Carbon $carbon 対象日付
     * @return string|null 祝日名 (祝日でない場合 null)
     */
    private function getHolidayName(Carbon $carbon): ?string
    {
        list($y, $m, $d, $w) = [$carbon->year, $carbon->month, $carbon->day, $carbon->dayOfWeek];
        if ($m === 1 && $d === 1) {
            return '元旦';
        } elseif (($y < 2000 && $m === 0 && $d === 15) || ($y > 1999 && $m === 1 && $d >= 8 && $d <= 14 && $w === Carbon::MONDAY)) {
            return '成人の日';
        } elseif ($m === 2 && $d === 11) {
            return '建国記念の日';
        } elseif (($y > 2018 && $m === 2 && $d === 23) || ($y > 1988 && $y < 2019 && $m === 12 && $d === 23)) {
            return '天皇誕生日';
        } elseif ($this->isShunbun($y, $m, $d)) {
            return '春分の日';
        } elseif ($m === 4 && $d == 29) {
            return '昭和の日';
        } elseif ($m === 5 && $d === 3) {
            return '憲法記念日';
        } elseif ($m === 5 && $d === 4) {
            return 'みどりの日';
        } elseif ($m === 5 && $d === 5) {
            return 'こどもの日';
        } elseif (($y > 1995 && $y < 2003 && $m === 7 && $d === 20) || ($y > 2002 && $y !== 2021 && $m === 7 && $d >= 15 && $d <= 21 && $w === Carbon::MONDAY) || ($y === 2021 && $m === 7 && $d === 22)) {
            return '海の日';
        } elseif (($y > 2015 && $y !== 2021 && $m === 8 && $d === 11) || ($y === 2021 && $m === 8 && $d === 8)) {
            return '山の日';
        } elseif (($y < 2003 && $m === 9 && $d === 15) || ($y > 2002 && $m == 9 && $d >= 15 && $d <= 21 && $w === Carbon::MONDAY)) {
            return '敬老の日';
        } elseif ($this->isShubun($y, $m, $d)) {
            return '秋分の日';
        } elseif (($y < 2000 && $m === 10 && $d === 10) || ($y > 1999 && $y !== 2021 && $m === 10 && $d >= 8 && $d <= 14 && $w === Carbon::MONDAY) || ($y === 2021 && $m === 7 && $d === 23)) {
            return $y > 2019 ? 'スポーツの日' : '体育の日';
        } elseif ($m === 11 && $d === 3) {
            return '文化の日';
        } elseif ($m === 11 && $d === 23) {
            return '勤労感謝の日';
        } else {
            return null;
        }
    }

    /**
     * 春分の日かどうかを判定して返す.
     *
     * @param int $y 年
     * @param int $m 月
     * @param int $d 日
     * @return bool 春分の日かどうか
     */
    private function isShunbun(int $y, int $m, int $d): bool
    {
        $arg1 = $y < 1980 ? 20.8357 : 20.8431;
        $arg2 = $y < 1980 ? 1983 : 1980;
        return $m === 3 && floatval($d) === floor($arg1 + 0.242194 * ($y - 1980) - floor(($y - $arg2) / 4.0));
    }

    /**
     * 秋分の日かどうかを判定して返す.
     *
     * @param int $y 年
     * @param int $m 月
     * @param int $d 日
     * @return bool 秋分の日かどうか
     */
    private function isShubun(int $y, int $m, int $d): bool
    {
        $arg1 = $y < 1980 ? 23.2588 : 23.2488;
        $arg2 = $y < 1980 ? 1983 : 1980;
        return $m === 9 && floatval($d) === floor($arg1 + 0.242194 * ($y - 1980) - floor(($y - $arg2) / 4.0));
    }

    /**
     * 対象日が振替休日かを判定して返す.
     *
     * @param Carbon $target 対象日
     * @return bool 振替休日かどうか
     */
    private function isExtra(Carbon $target): bool
    {
        // 前日が日曜かつ祝日であるならば振替休日
        $carbon = $target->copy();
        $carbon->subDay();
        if ($this->getHolidayName($carbon) !== null) {
            if ($carbon->dayOfWeek === Carbon::SUNDAY) {
                return true;
            }

            // 2007 年以後では日曜の祝日が発生した場合次の平日が振替休日となる: GW の振替休日判定が増える
            if ($carbon->year > 2006) {
                // 前日と後日が祝日であったならば挟まれた日も祝日
                $carbon->addDays(2);  // 対象日の後日に移動
                if ($this->getHolidayName($carbon) !== null) {
                    return true;
                }

                // 前々日から日曜の祝日を順に探索. 日曜でなく祝日だけ満たしていたらその前の日を探索し続ける. 祝日でなくなったら終了
                $carbon->subDays(3);  // 後日から前々日へシフト
                while ($this->getHolidayName($carbon) !== null) {
                    if ($carbon->dayOfWeek === Carbon::SUNDAY) {
                        return true;
                    }
                    $carbon->subDay();
                }
            }
        }
        return false;
    }
}

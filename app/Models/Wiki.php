<?php

namespace App\Models;

use cebe\markdown\GithubMarkdown;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Wiki
 *
 * @property int $id
 * @property string $markdown
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $html
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $histories
 * @property-read int|null $histories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki newQuery()
 * @method static \Illuminate\Database\Query\Builder|Wiki onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wiki whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Wiki withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Wiki withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\History $last_history
 * @property-read string $title
 */
class Wiki extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['markdown'];

    /**
     * モデルの配列形態に追加するアクセサ.
     *
     * @var array
     */
    protected $appends = ['title', 'html'];

    /**
     * 履歴との HasMany リレーションを構築する.
     *
     * @return HasMany Histories
     */
    public function histories(): HasMany
    {
        return $this->hasMany('App\Models\History');
    }

    /**
     * Markdown のタイトル部分のみを取得する.
     *
     * @return string タイトル
     */
    public function getTitleAttribute(): string
    {
        $lines = explode("\n", $this->markdown);
        if (count($lines) === 0) {
            return "";
        }

        // 厳密ではないがこの用途ならばこれで十分とみる
        return str_replace("# ", "", $lines[0]);
    }

    /**
     * Markdown の HTML 表現を取得する.
     *
     * @return string HTML 表現
     */
    public function getHtmlAttribute(): string
    {
        $html = (new GithubMarkdown)->parse($this->markdown);
        $pattern = '/<img src="([^"]+)" alt="([^"]+)"[^>]+>/';
        $replacement = '<a href="/storage/image/$1" title="$2"><img src="/storage/thumbnail/$1" alt="$2" class="rounded img-fluid" data-rjs="2"></a>';
        $html = preg_replace($pattern, $replacement, $html);
        $pattern = '/<table.*?>/';
        $replacement = '<table class="table table-bordered table-sm">';
        $html = preg_replace($pattern, $replacement, $html);
        return $html;
    }

    /**
     * 最後の履歴を返す.
     *
     * @return History 最後の履歴
     */
    public function getLastHistoryAttribute(): History
    {
        return History::whereWikiId($this->id)->orderByDesc('created_at')->firstOrFail();
    }
}

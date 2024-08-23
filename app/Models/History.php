<?php

namespace App\Models;

use cebe\markdown\GithubMarkdown;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\History
 *
 * @property int $id
 * @property int $wiki_id
 * @property string $markdown
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|History newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History query()
 * @method static \Illuminate\Database\Eloquent\Builder|History whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereWikiId($value)
 * @mixin \Eloquent
 * @property-read string $html
 * @property-read \App\Models\History|null $next
 * @property-read \App\Models\History|null $prev
 * @property-read int $rev_no
 */
class History extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['wiki_id', 'markdown', 'created_at'];

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
     * 何番目の履歴かを返す.
     *
     * @return int 何番目の履歴か
     */
    public function getRevNoAttribute(): int
    {
        return History::whereWikiId($this->wiki_id)
            ->where('created_at', '<=', $this->created_at)
            ->count();
    }

    /**
     * 前の履歴を返す. 存在しなければ null を返す.
     *
     * @return History|null 前の履歴
     */
    public function getPrevAttribute(): ?History
    {
        return History::whereWikiId($this->wiki_id)
            ->where('created_at', '<', $this->created_at)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * 次の履歴を返す. 存在しなければ null を返す.
     *
     * @return History|null 次の履歴
     */
    public function getNextAttribute(): ?History
    {
        return History::whereWikiId($this->wiki_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at')
            ->first();
    }
}


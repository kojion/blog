<?php

namespace App\Models;

use cebe\markdown\GithubMarkdown;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $year
 * @property int $month
 * @property int $day
 * @property string $name
 * @property string $markdown
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $comments_count
 * @property-read string $first_image_url
 * @property-read string $html
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Query\Builder|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|Post withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Post withoutTrashed()
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['year', 'month', 'day', 'name', 'markdown'];

    /**
     * キャストする項目を定義する.
     *
     * @var array
     */
    protected $casts = ['year' => 'integer', 'month' => 'integer', 'day' => 'integer', 'count' => 'integer'];

    /**
     * モデルの配列形態に追加するアクセサ.
     *
     * @var array
     */
    protected $appends = ['html', 'first_image_url'];

    /**
     * タグとの ManyToMany リレーションを構築する.
     *
     * @return BelongsToMany Tag
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Tag');
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
     * 最初に登場する横長の画像ファイル URL を返却する.
     *
     * @return string 最初に登場する横長の画像ファイル URL
     */
    public function getFirstImageUrlAttribute(): string
    {
        preg_match('/!\[.+]\((.+)\)/', $this->markdown, $group);
        if (!count($group)) {
            return '';
        }
        foreach (array_slice($group, 1) as $image) {
            $image = Image::query()->where(compact('image'))->first();
            if ($image !== null && $image->width >= $image->height) {
                return url("storage/thumbnail/$image->image");
            }
        }
        return '';
    }
}

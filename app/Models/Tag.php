<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property int $color
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory;

    /**
     * タグにおける color に対応する Bootstrap クラス名.
     */
    const BOOTSTRAP_CLASSES = [
        '1' => "primary",
        '2' => "secondary",
        '3' => "success",
        '4' => "danger",
        '5' => "warning",
        '6' => "info",
        '7' => "light",
        '8' => "dark"
    ];

    protected $fillable = ['name', 'color', 'rank'];

    /**
     * 記事との ManyToMany リレーションを構築する.
     *
     * @return BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany('App\Models\Post');
    }

    /**
     * 画像との ManyToMany リレーションを構築する.
     *
     * @return BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('App\Models\Image');
    }
}

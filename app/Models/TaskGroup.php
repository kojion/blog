<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TaskGroup
 *
 * @property int $id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|TaskGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TaskGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TaskGroup withoutTrashed()
 * @mixin \Eloquent
 */
class TaskGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'rank'];

    /**
     * Tasks との HasMany リレーションを構築する.
     *
     * @return HasMany Tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany('App\Models\Task')->orderByDesc('created_at');
    }

    /**
     * Tasks との HasMany リレーションを構築する.
     *
     * @return HasMany Tasks
     */
    public function tasksWithoutDoneAt(): HasMany
    {
        return $this->hasMany('App\Models\Task')
            ->whereNull('done_at')
            ->orderByDesc('created_at');
    }
}

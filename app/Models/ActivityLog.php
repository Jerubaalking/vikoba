<?php

namespace App\Models;


use App\Models\Traits\FilterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\Activitylog\Models\Activity;
use Spatie\String\Str;

/**
 * App\Models\ActivityLog
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read mixed $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Activitylog\Models\Activity inLog($logNames)
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property int|null $subject_id
 * @property string|null $subject_type
 * @property int|null $causer_id
 * @property string|null $causer_type
 * @property \Illuminate\Support\Collection $properties
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property mixed attributes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityLog whereUpdatedAt($value)
 */
class ActivityLog extends Activity
{

    protected $table = 'activity_log';

    use FilterTrait, SoftDeletes, SearchableTrait;

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [

        'columns' => [
            'log_name' => 5,
            'description' => 5,
        ]
    ];

    public function __toString()
    {

        return $this->attributes['description'];
    }

    /**
     * @return array
     */
    public static function get_field_mappings()
    {

        return ['description', 'subject', 'flag', 'created_at', 'logger', 'revisions', 'payload'];
    }

    /**
     * Get the revisions/ changes done for this subject
     *
     * @return Collection
     * @throws \Spatie\String\Exceptions\ErrorCreatingStringException
     */
    public function getRevisionsAttribute(): Collection
    {
        $collection = [];

        if (!empty(array_intersect(['attributes'], $this->getChangesAttribute()->keys()->toArray()))) {

            if (count($this->getOldAttribute()) > 0) {

                foreach ($this->getOldAttribute() as $key => $old) {

                    if ($old != $this->getNewAttribute()->only($key)->first()) {

                        $message = new Str(trans('field.' . $key));

                        $item = ucwords($message
                            ->replace('_id', '')
                            ->replace('field.', '')
                            ->replace('_', ' '));

                        $collection[$item] = [

                            'old' => $old,
                            'new' => $this->getNewAttribute()->only($key)->first(),
                        ];


                    }
                }

            }

        }

        return new Collection($collection);

    }

    public function getChangesAttribute(): Collection
    {

        $changes = new Collection(array_filter($this->properties->toArray(), function ($key) {
            return in_array($key, ['attributes', 'old']);
        }, ARRAY_FILTER_USE_KEY));

        return $changes;

    }

    /**
     * @return Collection
     */
    public function getOldAttribute(): Collection
    {

        return new Collection($this->getChangesAttribute()->get('old'));
    }

    /**
     * @return Collection
     */
    public function getNewAttribute(): Collection
    {

        return new Collection($this->getChangesAttribute()->get('attributes'));
    }


    /**
     * @return Collection
     */
    public function getPayloadAttribute()
    {
        return $this->properties->except(['attributes', 'old']);
    }


    public static function clearFrom($user, $date = null, $logname = null)
    {
        self::whereCauserId($user)
            ->when($date !== null && $date instanceof Carbon, function ($query) use ($date) {
                return $query->whereDate('created_at', $date);
            })
            ->when($logname !== null, function ($query) use ($logname) {
                return $query->where('log_name', $logname);
            })
            ->forceDelete();
    }

    /**
     * @return array
     */
    public function getFlagAttribute()
    {

        return $this->attributes['log_name'];
    }

    /**
     * @param null $request
     * @param bool $pagination
     * @return mixed
     */
    public static function getFromQuery($request = null, $pagination = false)
    {

        $request = !is_null($request) ? $request : \Request::createFromGlobals();

        $query = static::when(!empty($request->get('q')), function ($q) use ($request) {
            return $q->search(str_replace('"', '', $request->query('q')), null, true);
        })
            ->sortBy($request->query('sort'))
            ->whereBy('causer_id', $request->get('user'))
            ->whereBy('subject_id', $request->get('subject'))
            ->dateBetween('created_at', str_replace(' ', '', $request->query('range')));

        return $pagination ? $query->distinct() : $query->distinct()->get();
    }


}

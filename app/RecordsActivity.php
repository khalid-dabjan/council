<?php

namespace App;

/**
 *
 * @author khaliddabjan
 */
trait RecordsActivity
{

    public static function bootRecordsActivity()
    {
        if (auth()->guest())
            return;
        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function($model){
            $model->activities()->delete();
        });
    }

    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }

    /**
     * 
     */
    public function recordActivity($event)
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * 
     * @param type $event
     * @return type
     */
    public function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());
        return "{$event}_{$type}";
    }

}

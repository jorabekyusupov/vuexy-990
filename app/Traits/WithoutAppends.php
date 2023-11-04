<?php

namespace App\Traits;


trait WithoutAppends
{

    public static $withoutAppends = false;
    /**
     * @var $query \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAppends($query)
    {
        self::$withoutAppends = true;


        return $query;
    }
    protected function getArrayableAppends()
    {
        if (self::$withoutAppends){
            return [];
        }

        return parent::getArrayableAppends();
    }
}
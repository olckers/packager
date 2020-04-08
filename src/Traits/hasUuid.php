<?php
namespace olckerstech\core\src\traits;

use Illuminate\Support\Str;

trait hasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

}

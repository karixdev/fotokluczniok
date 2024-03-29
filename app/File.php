<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class File extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->{$user->getKeyName()} = (string) Uuid::uuid4();
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

    public function gallery()
    {
        return $this->belongsTo('App\Gallery');
    }
}

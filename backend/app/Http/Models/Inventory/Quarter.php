<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quarter extends Model
{
    protected $connection = 'pg_inventory';
    protected $table = 'quarters';
    // public $timestamps = false;
    use SoftDeletes;
    protected $fillable = ['year', 'quarter'];

    // public function createdBy()
    // {
    //     return $this->hasOne(\App\User::class, 'id', 'created_by');
    // }

    // public function updatedBy()
    // {
    //     return $this->hasOne(\App\User::class, 'id', 'updated_by');
    // }
    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    //     'updated_at' => 'datetime:Y-m-d H:i',
    //     'deleted_at' => 'datetime:Y-m-d H:i',
    // ];
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}
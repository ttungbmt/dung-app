<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetquaDm extends Model
{
    use HasFactory;

    protected $table = 'ketqua_dm';

    protected $fillable = ['ids', 'keywords'];

    public $timestamps = false;

    protected $casts = [
        'ids' => 'array'
    ];
}

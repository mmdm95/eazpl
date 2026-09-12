<?php

namespace Eazpl\App\Models;

use Illuminate\Database\Eloquent\Model;

final class LabelTemplate extends Model
{
    protected $fillable = ['name', 'state'];

    protected $casts = ['state' => 'array'];
}

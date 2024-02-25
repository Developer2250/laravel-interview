<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionData extends Model
{
    use HasFactory;

    protected $fillable = ['prize_id', 'winners','actual_probability'];
}

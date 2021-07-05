<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Grid
 * @package App\Models
 *
 * @property $id
 * @property $position_x
 * @property $position_y
 * @property $has_obstacle
 */
class Coordinate extends Model
{
    use HasFactory;

    const X_AXIS = 'position_x';
    const Y_AXIS = 'position_y';
    const HAS_OBSTACLE = 'has_obstacle';

    protected $fillable = [
        'position_x',
        'position_y',
        'has_obstacle',
    ];
}

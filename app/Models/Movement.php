<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Movement
 * @package App\Models
 *
 * @property $command
 * @property $instruction
 * @property $coordinate_id
 * @property $direction
 * @property $is_initial
 * @property $success
 * @property $reason
 */
class Movement extends Model
{
    use HasFactory;

    const COMMAND = 'command';
    const INSTRUCTION = 'instruction';
    const COORDINATES_ID = 'coordinates_id';
    const DIRECTION = 'direction';
    const IS_INITIAL = 'is_initial';
    const SUCCESS = 'success';
    const REASON = 'reason';

    protected $fillable = [
        'command',
        'instruction',
        'coordinates_id',
        'direction',
        'is_initial',
        'success',
        'reason',
    ];

    public function coordinates()
    {
        return $this->belongsTo(Coordinate::class);
    }
}

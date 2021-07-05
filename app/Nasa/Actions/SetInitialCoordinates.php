<?php

namespace App\Nasa\Actions;

use App\Models\Coordinate;
use App\Models\Movement;

class SetInitialCoordinates implements BaseAction
{
    protected $coordinateX;

    protected $coordinateY;

    protected $direction;

    /**
     * SetInitialCoordinates constructor.
     * @param $coordinateX
     * @param $coordinateY
     * @param $direction
     */
    public function __construct($coordinateX, $coordinateY, $direction)
    {
        $this->coordinateX = $coordinateX;
        $this->coordinateY = $coordinateY;
        $this->direction = $direction;
    }

    public function handle()
    {
        $already_intialized = Movement::where(Movement::IS_INITIAL, true)->first();

        if ($already_intialized) {
            return json_encode([
                'error'   => true,
                'message' => 'Initial coordinates already set',
            ]);
        }

        /** @var Coordinate $starting_point */
        $starting_point = Coordinate::create([
            Coordinate::X_AXIS       => $this->coordinateX,
            Coordinate::Y_AXIS       => $this->coordinateY,
            Coordinate::HAS_OBSTACLE => false,
        ]);

        $movement = Movement::create([
            Movement::COORDINATES_ID => $starting_point->id,
            Movement::SUCCESS        => true,
            Movement::DIRECTION      => $this->direction,
            Movement::IS_INITIAL     => true,
        ]);

        //very ugly "hack" to get the coordinates right after created
        $movement->coordinates;

        return json_encode([
            'success'     => true,
            'coordinates' => $starting_point,
            'movements'   => $movement,
        ]);
    }
}

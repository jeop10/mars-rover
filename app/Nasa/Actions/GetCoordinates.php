<?php

namespace App\Nasa\Actions;

use App\Models\Movement;

class GetCoordinates implements BaseAction
{
    public function handle()
    {
        $coordinates = false;
        $initial_movement = Movement::where('is_initial', 1)->first();

        if ($initial_movement) {
            $coordinates = $initial_movement->coordinates;
        }

        return $coordinates;
    }
}

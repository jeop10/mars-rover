<?php

namespace App\Nasa\Actions;

use App\Models\Coordinate;
use App\Models\Movement;

class GetPreviousMovements implements BaseAction
{
    public function handle()
    {
        $data = [
            'movements' => Movement::with('coordinates')->orderBy('id', 'desc')->take(10)->get()->toArray(),
            'obstacles' => Coordinate::orderBy('id', 'asc')->where('has_obstacle', 1)->take(10)->get()->toArray(),
        ];

        return json_encode($data);
    }
}

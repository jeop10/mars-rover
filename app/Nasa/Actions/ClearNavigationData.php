<?php

namespace App\Nasa\Actions;

use App\Models\Coordinate;
use App\Models\Movement;

class ClearNavigationData implements BaseAction
{

    public function handle()
    {
        Movement::query()->delete();
        Coordinate::query()->delete();

        return json_encode(['success' => true]);
    }
}

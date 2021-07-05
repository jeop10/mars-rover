<?php

namespace App\Nasa\Actions;

use App\Models\Coordinate;
use App\Models\Movement;
use App\Nasa\Support\Navigation;

class SendCommand implements BaseAction
{
    protected $command;

    protected $enableObstacles;

    /**
     * SendCommand constructor.
     */
    public function __construct($command, $enable_obstacles)
    {
        $this->command = $command;
        $this->enableObstacles = $enable_obstacles;
    }

    public function handle()
    {
        $instructions = str_split($this->command);
        $navigation = new Navigation($this->command, $this->enableObstacles);
        $success = true;

        foreach ($instructions as $instruction) {
            $check = $navigation->moveInDirection($instruction);

            if (!is_null($check)) {
                break;
            }
        }

        $data = [
            'success'   => $success,
            'movements' => Movement::with('coordinates')->orderBy('id', 'desc')->take(10)->get()->toArray(),
            'obstacles' => Coordinate::orderBy('id', 'asc')->where('has_obstacle', 1)->take(10)->get()->toArray(),
        ];

        return response()->json($data);
    }
}

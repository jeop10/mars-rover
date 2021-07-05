<?php

namespace App\Nasa\Support;

use App\Models\Coordinate;
use App\Models\Movement;

class Navigation
{
    const FORWARD = 'F';

    const LEFT = 'L';

    const RIGHT = 'R';

    const OBSTACLE_CHANCE = 20;

    protected $command;

    protected $startX;

    protected $startY;

    protected $direction;

    protected $stepX;

    protected $stepY;

    protected $lastMovement;

    const TEST_COMMAND = 'FFRRFFFRL';

    /** @var Coordinate $currentCoordinate */
    protected $currentCoordinate;

    protected $instruction;

    protected $obstaclesEnabled = true;

    /**
     * Navigation constructor.
     */
    public function __construct($command, $obstaclesEnabled)
    {
        $this->command = $command;

        $this->movements = [];

        $this->lastMovement = Movement::with('coordinates')
            ->whereNotNull('coordinates_id')
            ->orderBy('id', 'desc')
            ->first();

        $this->startX = $this->lastMovement->coordinates->position_x;
        $this->startY = $this->lastMovement->coordinates->position_y;
        $this->stepX = $this->startX;
        $this->stepY = $this->startY;
        $this->direction = $this->lastMovement->direction;
        $this->obstaclesEnabled = $obstaclesEnabled;
    }

    public function moveInDirection($instruction)
    {
        $this->instruction = $instruction;

        if ($this->instruction == Navigation::FORWARD) {
            switch ($this->direction) {
                case Compass::NORTH:
                    $this->stepY += 1;
                    break;
                case Compass::SOUTH:
                    $this->stepY -= 1;
                    break;
                case Compass::EAST:
                    $this->stepX += 1;
                    break;
                case Compass::WEST:
                    $this->stepX -= 1;
                    break;
            }
        }

        if ($this->instruction == Navigation::LEFT) {
            switch ($this->direction) {
                case Compass::NORTH:
                    $this->stepX -= 1;
                    break;
                case Compass::SOUTH:
                    $this->stepX += 1;
                    break;
                case Compass::EAST:
                    $this->stepY += 1;
                    break;
                case Compass::WEST:
                    $this->stepY -= 1;
                    break;
            }
        }

        if ($this->instruction == Navigation::RIGHT) {
            switch ($this->direction) {
                case Compass::NORTH:
                    $this->stepX += 1;
                    break;
                case Compass::SOUTH:
                    $this->stepX -= 1;
                    break;
                case Compass::EAST:
                    $this->stepY -= 1;
                    break;
                case Compass::WEST:
                    $this->stepY += 1;
                    break;
            }
        }

        if (Compass::isPointOutOfBounds($this->stepX, $this->stepY)) {
            return Movement::create([
                Movement::COMMAND        => $this->command,
                Movement::DIRECTION      => $this->direction,
                Movement::SUCCESS        => false,
                Movement::REASON         => sprintf('Instruction: %s Position (%d, %d) out of bound. Breaking command', $this->instruction, $this->stepX, $this->stepY ),
            ]);
        }

        return $this->moveToPosition();
    }

    public function moveToPosition()
    {
        if ($this->isObstacle()) {
            $collision = Movement::create([
                Movement::COMMAND        => $this->command,
                Movement::INSTRUCTION    => $this->instruction,
                Movement::COORDINATES_ID => $this->currentCoordinate->id,
                Movement::DIRECTION      => $this->direction,
                Movement::SUCCESS        => false,
                Movement::REASON         => 'Has Obstacle. Breaking command. Returning to previous safe point',
            ]);

            $reset = Movement::create([
                Movement::COMMAND        => $this->command,
                Movement::COORDINATES_ID => $this->lastMovement->coordinates_id,
                Movement::DIRECTION      => $this->direction,
                Movement::SUCCESS        => true,
                Movement::REASON         => 'Returning to safe position',
            ]);

            return $reset;
        }

         $this->lastMovement = Movement::create([
            Movement::COMMAND        => $this->command,
            Movement::INSTRUCTION    => $this->instruction,
            Movement::COORDINATES_ID => $this->currentCoordinate->id,
            Movement::DIRECTION      => $this->direction,
            Movement::SUCCESS        => true,
            Movement::REASON         => 'Instruction: '.$this->instruction,
        ]);
    }

    public function isObstacle(): bool
    {
        /** @var Coordinate $coordinate */
        $coordinate = Coordinate::where(Coordinate::X_AXIS, $this->stepX)
            ->where(Coordinate::Y_AXIS, $this->stepY)
            ->first();

        $this->currentCoordinate = $coordinate;

        if ($coordinate && $coordinate->has_obstacle) {
            return true;
        }

        if ($coordinate && !$coordinate->has_obstacle) {
            return false;
        }

        $has_obstacle = false;

        if ($this->obstaclesEnabled) {
            //Coordinate doesnt exist must generate
            $random = random_int(0, 100);
            if ($random < self::OBSTACLE_CHANCE) {
                $has_obstacle = true;
            }
        }

        $this->currentCoordinate = Coordinate::create([
            Coordinate::X_AXIS       => $this->stepX,
            Coordinate::Y_AXIS       => $this->stepY,
            Coordinate::HAS_OBSTACLE => $has_obstacle,
        ]);

        return $has_obstacle;
    }
}

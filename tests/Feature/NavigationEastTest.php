<?php

namespace Tests\Feature;

use App\Models\Coordinate;
use App\Models\Movement;
use App\Nasa\Support\Compass;
use App\Nasa\Support\Navigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NavigationEastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_normal()
    {
        $origin = [
            'coordinates' => '1,5',
            'direction'   => Compass::EAST,
        ];

        $this->postJson('/api/set-coordinates', $origin);

        $command = [
            'command'   => Navigation::TEST_COMMAND,
            'obstacles' => false,
        ];

        $response = $this->postJson('api/send-command', $command);

        $coordinates = Coordinate::all();

        $positions = [
            [1, 5],
            [2, 5],
            [3, 5],
            [3, 4],
            [3, 3],
            [4, 3],
            [5, 3],
            [6, 3],
            [6, 2],
        ];

        $error = false;
        foreach ($positions as $position) {
            $this->assertDatabaseHas(Coordinate::class, [Coordinate::X_AXIS=> $position[0] , Coordinate::Y_AXIS => $position[1]]);
        }

        $this->assertDatabaseCount(Movement::class, 10);

        $last_movement = Movement::orderBy('id', 'desc')->first();

        $last_movement_error_check = false;
        if ($last_movement->coordinates->position_x != 6 || $last_movement->coordinates->position_y != 3) {
            $last_movement_error_check = true;
        }

        $this->assertFalse($last_movement_error_check);

        $data = [
            'movements' => Movement::orderBy('id', 'desc')->get()->toArray(),
            'obstacles' => Coordinate::orderBy('id', 'asc')->where('has_obstacle', 1)->get()->toArray(),
        ];

        $response->assertJson($data);
    }

    public function test_withObstacles()
    {
        //Create the coordinates
        $coordinates = [
            [Coordinate::X_AXIS => 1, Coordinate::Y_AXIS => 5, Coordinate::HAS_OBSTACLE => false], //initial
            [Coordinate::X_AXIS => 3, Coordinate::Y_AXIS => 4, Coordinate::HAS_OBSTACLE => true], //obstacle
        ];

        foreach ($coordinates as $coordinate) {
            Coordinate::create($coordinate);
        }

        //Create First Movement
        Movement::create([
            Movement::DIRECTION      => Compass::EAST,
            Movement::COORDINATES_ID => Coordinate::orderBy('id')->first()->id,
            Movement::IS_INITIAL     => 1,
            Movement::SUCCESS        => true,
        ]);

        $command = [
            'command'   => Navigation::TEST_COMMAND,
            'obstacles' => false,
        ];

        $response = $this->postJson('api/send-command', $command);

        $obstacles = Coordinate::orderBy('id', 'asc')->where('has_obstacle', 1)->get()->toArray();

        $this->assertDatabaseCount(Movement::class, 5);
        $this->assertCount(1, $obstacles);

        $data = [
            'movements' => Movement::orderBy('id', 'desc')->get()->toArray(),
            'obstacles' => $obstacles,
        ];

        $response->assertJson($data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Nasa\Actions\ClearNavigationData;
use App\Nasa\Actions\GetCoordinates;
use App\Nasa\Actions\GetPreviousMovements;
use App\Nasa\Actions\SendCommand;
use App\Nasa\Actions\SetInitialCoordinates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function index()
    {
        $data = [];
        return view('control-panel', $data);
    }

    public function getCoordinates()
    {
        $action = new GetCoordinates();

        return $action->handle();
    }

    public function getPreviousMovements()
    {
        $action = new GetPreviousMovements();

        return $action->handle();
    }

    public function setCoordinates(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'coordinates' => 'required',
            'direction'   => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->getMessageBag(),
            ]);
        }

        $coordinates = explode(',', $data['coordinates']);

        $action = new SetInitialCoordinates((int)$coordinates[0], (int)$coordinates[1], $data['direction']);

        return $action->handle();
    }

    public function sendCommand(Request $request)
    {
        $command = $request->get('command');
        $command = Str::upper($command);

        $enable_obstacles = $request->get('obstacles', true);

        $validator = Validator::make($request->all(), [
            'command' => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode([
                'error' => true,
                'message' => $validator->getMessageBag(),
            ]);
        }

        $command_check = preg_match('/^[FfLlRr]{0,9}$/', $command);

        if (!$command_check) {
            return json_encode([
                'error' => true,
                'message' => 'The command is invalid, please verify it',
            ]);
        }

        $action = new SendCommand($command, $enable_obstacles);

        return $action->handle();
    }

    public function clearNavigationData()
    {
        $action = new ClearNavigationData();

        return $action->handle();
    }
}

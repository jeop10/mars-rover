<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <title>Rovers connection system.</title>
</head>
<body>

<main class="container" id="roverApp">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
            <span class="fs-4">Rovers connection system.</span>
        </a>
    </header>

    <div class="row mb-4">
        <div class="col">
            <h3>
                Command and control
            </h3>
        </div>
    </div>


    <div class="row" v-cloak>

        <div class="col">
            <div class="card">
                <h5 class="card-header">Console Input</h5>
                <div class="card-body">
                    <div class="form mb-4">
                        <label for="colFormLabelSm" class="col col-form-label col-form-label-sm no-wrap">Starting
                            Point</label>
                        <div class="form-group row align-items-center">
                            <div class="col">
                                <input type="number" min="0" max="199" pattern="\d*"
                                       v-model="startPointX" :change="checkPositions()"
                                       class="form-control form-control-sm" placeholder="X"
                                       :disabled="startPointEstablished">
                            </div>
                            <div class="col">
                                <input type="number" min="0" v-model="startPointY" :change="checkPositions()"
                                       class="form-control form-control-sm" placeholder="Y"
                                       :disabled="startPointEstablished">
                            </div>
                            <div class="col-4">
                                <select name="direction" id="direction" class="form-select form-select-sm"
                                        v-model="startDirection" :disabled="startPointEstablished">
                                    <option value="N">North</option>
                                    <option value="S">South</option>
                                    <option value="E">East</option>
                                    <option value="W">West</option>
                                </select>
                            </div>
                            <div class="col d-flex justify-content-center">
                                <div class="btn btn-sm btn-success" v-on:click="saveStartingPoint()"
                                     :class="{disabled: startPointEstablished}">SEND
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form mb-4">
                        <div class="form-group row align-items-center">
                            <label class="col-3 col-form-label col-form-label-sm">Command:</label>
                            <div class="col-6">
                                <input v-model="command" type="text" class="form-control form-control-sm"
                                       :change="checkCommand()"
                                       :class="{'is-invalid': commandInvalid}"
                                       placeholder="Introduce command">
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <div class="btn btn-sm btn-success" v-on:click="sendCommand()">SEND</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="btn btn-small btn-danger" v-on:click="clearNavigation()">Clear navigation data</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card" style="height: 70vh;">
                <h5 class="card-header">Console Output</h5>
                <ul class="list-group list-group-flush overflow-auto">
                    <li class="list-group-item" v-for="movement in movements">
                        <div class="d-flex flex-column">
                            <samp class="d-flex flex-row mb-2 justify-content-between">
                                <span>
                                    Movement ID: @{{ movement.id }}
                                </span>
                                <span>
                                    Success: <span :class="{'text-success': movement.success, 'text-danger': !movement.success}">@{{ movement.success ? 'True' : 'False' }}</span>
                                </span>
                            </samp>
                            <samp class="mb-2" v-if="movement.coordinates">
                                Position (X,Y): @{{ movement.coordinates.position_x }},@{{ movement.coordinates.position_y }}
                            </samp>
                            <samp>
                                Reason: <span v-if="movement.is_initial">Initial Coordinates</span>
                                <span v-else>
                                    @{{ movement.reason }}
                                </span>
                            </samp>
                        </div>
                    </li>
                    <li class="list-group-item text-center" v-if="movements.length < 1">Awaiting output</li>
                </ul>
            </div>
        </div>

        <div class="col">
            <div class="card" style="height: 70vh;">
                <h5 class="card-header">Obstacles detected</h5>
                <ul class="list-group list-group-flush overflow-auto">
                    <li class="list-group-item" v-for="obstacle in obstacles">
                        <div class="d-flex flex-column">
                            <samp class="mb-2">
                                Position (X,Y): @{{ obstacle.position_x }},@{{ obstacle.position_y }}
                            </samp>
                        </div>
                    </li>
                    <li class="list-group-item text-center" v-if="obstacles.length < 1">Awaiting obstacle data</li>
                </ul>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>

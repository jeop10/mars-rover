require('./bootstrap');

window.Vue = require('vue/dist/vue.common.dev');

var app = new Vue({
    el: '#roverApp',
    data: {
        startPointX: '',
        startPointY: '',
        startDirection: 'N',
        startPointEstablished: false,
        sendingStartingPoint: false,
        command: '',
        sendingCommand: false,
        commandInvalid: false,
        movements: [],
        obstacles: []
    },
    mounted: function () {
        this.checkForInitialCoordinates();
    },
    methods: {
        checkForInitialCoordinates: function () {
            const vm = this;

            axios.get('/api/initial-coordinates')
                .then(function (response) {
                        var data = response.data;

                        if (data) {
                            vm.startPointX = data.position_x;
                            vm.startPointY = data.position_y;
                            vm.startPointEstablished = true;
                            vm.checkForMovements();
                        }

                        if (data.error) {
                            alert(data.message);
                        }
                    }
                );
        },
        checkForMovements: function () {
            const vm = this;

            axios.get('/api/previous-movements')
                .then(function (response) {
                        var data = response.data;

                        if (data) {
                            vm.movements = data.movements;
                            vm.obstacles = data.obstacles;
                        }

                        if (data.error) {
                            alert(data.message);
                        }
                    }
                );
        },
        saveStartingPoint: function () {
            const vm = this;
            if (this.startPointEstablished || this.sendingStartingPoint) {
                return;
            }

            this.sendingStartingPoint = true;

            if (parseInt(this.startPointX) >= 0 && parseInt(this.startPointY) >= 0 && this.startDirection) {
                axios.post('/api/set-coordinates', {
                    coordinates: this.startPointX + ',' + this.startPointY,
                    direction: this.startDirection
                }).then(function (response) {
                        var data = response.data;

                        if (data.success) {
                            vm.startPointEstablished = true;
                            vm.movements = _.concat(vm.movements, data.movements);

                            vm.movements = _.orderBy(vm.movements, ['id'], ['desc']);
                        }

                        if (data.error) {
                            alert(data.message);
                        }

                        vm.sendingStartingPoint = false;
                    }
                );
            }
        },
        sendCommand: function () {
            const vm = this;

            if (!vm.startPointEstablished) {
                alert("Please set the start point first");
                return;
            }

            this.sendingCommand = true;

            axios.post('/api/send-command', {command: vm.command})
                .then(function (response) {
                    var data = response.data;

                    if (data.success) {
                        vm.movements = data.movements;
                        vm.obstacles = data.obstacles;
                    }

                    if (data.error) {
                        alert(data.message);
                    }

                    vm.sendingCommand = false;
                }).catch(function (error) {
                    var prueba = 1;
                });
        },
        clearNavigation: function () {
            const vm = this;
            axios.post('/api/clear-navigation', {})
                .then(function (response) {
                        var data = response.data;

                        if (data.success) {
                            vm.startPointEstablished = false;
                            vm.startPointX = '';
                            vm.startPointY = '';
                            vm.movements = [];
                            vm.obstacles = [];

                            alert("Navigation data cleared");
                        }
                    }
                );
        },
        checkPositions: function () {
            var regex = /^[0-9]*(?:\.\d{1,2})?$/;
            if (!regex.test(this.startPointX)) {
                this.startPointX = 0;
            }

            if (!regex.test(this.startPointY)) {
                this.startPointY = 0;
            }
        },
        checkCommand: function () {
            this.commandInvalid = false;
            var regex = /^[FfLlRr]{0,9}$/;
            if (!regex.test(this.command)) {
                this.commandInvalid = true;
            }
        }
    }
})

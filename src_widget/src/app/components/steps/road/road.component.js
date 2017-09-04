(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function (api) {
                var self = this;
                self.roads = [];
                this.isSatisfy = null;

                function didLoadRoads(json) {
                    self.roads = json.routes;
                }

                this.$onInit = function () {
                    if (self.userData.road.title === null) {
                        this.isSatisfy = false;
                    }

                    api.loadRoads().then(didLoadRoads)
                };

                // this.roads = [{
                //     image: '/img/dummy-road-1.png',
                //     title: 'Town roads',
                //     desc: "You'd prefer busier street, with stop-and-start traffic for your test drive."
                // }, {
                //     image: '/img/dummy-road-1.png',
                //     title: 'Country roads',
                //     desc: "Your'd prefer quiet roads out the way of the city traffic for your test drive."
                // }, {
                //     image: '/img/dummy-road-1.png',
                //     title: 'Motorway',
                //     desc: "You'd prefer faster roads where you can pick up the speed on your test drive."
                // }];

                this.expertChanged = function (expertTitle) {
                    self.userData.road.title = expertTitle;
                    self.satisfyStep();
                };


                this.satisfyStep = function () {
                    if (self.userData.road.title === null) {
                        this.isSatisfy = false;
                    } else {
                        this.isSatisfy = true;
                    }
                };

                this.completeStepInner = function () {
                    if (self.isSatisfy) {
                        self.completeStep({tabId: this.tabId});
                    }
                };
            },
            templateUrl: 'src/app/components/steps/road/road.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


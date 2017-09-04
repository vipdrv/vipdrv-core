(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                var dummyBeverages = [{
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Road 1',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Road 2',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Road 3',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Road 4',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }];

                $scope.items = dummyBeverages;

                self.$onInit = function () {
                    if (self.userData.road.title === null) {
                        this.isSatisfy = false;
                    }
                };

                $scope.itemChanged = function (itemTitle) {
                    console.log(itemTitle);
                    self.userData.road.title = itemTitle;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.road.title === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                $scope.nextStepInner = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
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


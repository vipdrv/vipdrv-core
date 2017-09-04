(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function ($scope, api) {
                var self = this;

                self.isStepValid = null;

                var dummyBeverages = [{
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Blue water 1',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Blue water 2',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Blue water 3',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Blue water 4',
                    description: 'Water is a transparent and nearly colorless chemical substance that is the main constituent of Earth'
                }];

                $scope.beverages = dummyBeverages;

                self.$onInit = function () {
                    if (self.userData.beverage.title === null) {
                        this.isSatisfy = false;
                    }
                };

                $scope.itemChanged = function (expertTitle) {
                    self.userData.beverage.title = expertTitle;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.userData.beverage.title === null) {
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
            templateUrl: 'src/app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


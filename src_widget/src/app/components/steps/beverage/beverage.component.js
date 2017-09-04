(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function ($scope, api) {
                var self = this;
                self.beverages = [];
                this.isSatisfy = null;

                var dummyBeverages = [{
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'George Reese',
                    description: 'As a certified automotive specialist, George will be happy to help you make the right decisions.'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Joe Rowe',
                    description: 'With over 10 years experience in the industry Joe has invaluable knowledge in give.'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Gregory May',
                    description: 'Head of sales'
                }, {
                    photo_url: '/img/dummy-drink-water.png',
                    title: 'Rhoda Hogan',
                    description: 'Rhoda has a bubbly personality to make any test drive a not to be missed experience.'
                }];

                $scope.beverages = dummyBeverages;

                function didLoadBeverages(json) {
                    self.beverages = json.beverages;
                }

                this.$onInit = function () {
                    if (self.userData.beverage.title === null) {
                        this.isSatisfy = false;
                    }

                    // api.loadBeverages().then(didLoadBeverages)
                };

                this.beverageChanged = function (expertTitle) {
                    self.userData.beverage.title = expertTitle;
                    self.satisfyStep();
                };

                this.satisfyStep = function () {
                    if (self.userData.beverage.title === null) {
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
            templateUrl: 'src/app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


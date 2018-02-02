(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope, dealerData, bookingData) {

                var self = this;
                self.isStepValid = null;
                self.dealerData = dealerData;
                self.bookingData = bookingData;

                self.$onInit = function () {
                    if (self.bookingData.road.name === null) {
                        this.isStepValid = false;
                    }
                };

                $scope.itemChanged = function ($event, id, img, name, description) {
                    var index = $event.target.className.indexOf('ngTruncateToggleText');
                    if (index > -1) {
                        return;
                    }

                    self.bookingData.road.id = id;
                    self.bookingData.road.img = img;
                    self.bookingData.road.name = name;
                    self.bookingData.road.description = description;
                    self.validateStep();
                };

                this.validateStep = function () {
                    if (self.bookingData.road.title === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                $scope.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
                $scope.skipStep = function () {
                    self.bookingData.road.id = 0;
                    self.bookingData.road.name = "Skipped";
                    self.completeStep({tabId: self.tabId});
                };
            },
            templateUrl: 'src/app/components/steps/road/road.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&'
            }
        });
})();


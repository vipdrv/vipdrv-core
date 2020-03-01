(function () {
    angular.module('myApp')
        .component('tdRoad', {
            controller: function ($scope, dealerData, bookingData) {

                var self = this;
                self.isStepValid = null;
                self.dealerData = dealerData;
                self.bookingData = bookingData;

                self.$onInit = function () {
                    self.validateStep();
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
                    if (self.bookingData.road.name) {
                        self.isStepValid = true;
                    } else  {
                        self.isStepValid = false;
                    }
                };

                self.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };
                self.skipStep = function () {
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


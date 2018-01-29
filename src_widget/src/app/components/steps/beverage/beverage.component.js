(function () {
    angular.module('myApp')
        .component('tdBeverage', {
            controller: function ($scope, $window, dealerData, bookingData) {
                var self = this;
                self.isStepValid = null;
                self.dealerData = dealerData;
                self.bookingData = bookingData;

                self.$onInit = function () {
                    if (self.bookingData.beverage.name === null) {
                        self.isStepValid = false;
                    }
                };

                self.itemChanged = function ($event, id, img, name, description) {
                    var index = $event.target.className.indexOf('ngTruncateToggleText');
                    if (index > -1) {
                        return;
                    }

                    self.bookingData.beverage.id = id;
                    self.bookingData.beverage.img = img;
                    self.bookingData.beverage.name = name;
                    self.bookingData.beverage.description = description;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.bookingData.beverage.name === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                self.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };

                self.skipStep = function () {
                    self.bookingData.beverage.id = 0;
                    self.bookingData.beverage.name = "Skipped";
                    self.completeStep({tabId: self.tabId});
                };
            },
            templateUrl: 'src/app/components/steps/beverage/beverage.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&'
            }
        });
})();


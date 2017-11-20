(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope) {
                var self = this;

                self.dateChanged = function (oldValue, newValue) {
                    var arr = newValue._i.split(' ');
                    // var dayOfWeek = arr[1];

                    self.userData.calendar.date = arr[0];
                };

                self.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                };

                this.$onInit = function () {
                    self.openHours = 1;
                };

                self.isStepValid = true;
                self.timeIntervals = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 AM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];

                this.validateStep = function () {
                };

                $scope.nextStep = function () {
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };

                $scope.skipStep = function () {
                    self.completeStep({tabId: self.tabId});
                };
            },
            templateUrl: 'src/app/components/steps/date/date.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


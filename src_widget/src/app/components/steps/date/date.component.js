(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = false;
                self.minDateString = moment().subtract(1, 'day').format('YYYY-MM-DD');

                self.$onInit = function () {
                    self.dateImput = self.userData.calendar.date;
                    self.validateStep();
                };

                self.dateChanged = function (oldValue, newValue) {
                    var arr = newValue._i.split(' ');
                    self.userData.calendar.date = arr[0];
                    self.validateStep();
                };

                self.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                    self.validateStep();
                };

                self.timeIntervals = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 AM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM'];

                self.validateStep = function () {
                    if (self.userData.calendar.date != null && self.userData.calendar.time != null) {
                        self.isStepValid = true;
                    } else {
                        self.isStepValid = false;
                    }
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


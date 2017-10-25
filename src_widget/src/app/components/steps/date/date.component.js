(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = true;

                self.timeIntervals = ['9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

                self.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                };

                this.validateStep = function () {

                };

                $scope.nextStepInner = function () {
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
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


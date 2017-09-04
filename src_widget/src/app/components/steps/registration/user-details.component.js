(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope) {
                var self = this;

                self.isStepValid = null;

                this.$onInit = function () {
                    if (self.userData.user.email === null) {
                        self.isStepValid = false;
                    }
                };

                this.validateStep = function () {
                    self.isStepValid = true;
                    return;
                    if (self.userData.user.email === null) {
                        self.isStepValid = false;
                    } else {
                        self.isStepValid = true;
                    }
                };

                $scope.makeBooking = function () {
                    self.validateStep();

                    if (self.isStepValid) {
                        // self.completeStep({tabId: self.tabId});
                        self.completeForm();
                    }
                };
            },
            templateUrl: 'src/app/components/steps/registration/user-details.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&',
                completeForm: '&'
            }
        });
})();


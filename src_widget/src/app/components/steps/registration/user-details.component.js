(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, api) {
                var self = this;

                self.makeBooking = function () {
                    self.userData.firstName = $scope.firstName;
                    self.userData.lastName = $scope.lastName;
                    self.userData.email = $scope.email;
                    self.userData.phone = $scope.phone;

                    api.completeBooking(self.userData).then(function () {
                        self.completeForm();
                    });
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


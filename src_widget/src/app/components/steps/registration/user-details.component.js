(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, api) {
                var self = this;

                self.makeBooking = function () {
                    self.userData.user.firstName = $scope.firstName;
                    self.userData.user.lastName = $scope.secondName;
                    self.userData.user.email = $scope.email;
                    self.userData.user.phone = $scope.phone;

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


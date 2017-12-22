(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, api, dealerData, userData) {

                var self = this;
                self.dealerData = dealerData;
                self.userData = userData;

                self.makeBooking = function () {
                    self.userData.user.firstName = $scope.firstName;
                    self.userData.user.lastName = $scope.secondName;
                    self.userData.user.email = $scope.email;
                    self.userData.user.phone = $scope.phone;
                    self.userData.user.comment = $scope.comment;

                    api.completeBooking(self.userData).then(function () {
                    });
                    // TODO: add reaction on promise
                    self.completeForm();
                };
            },
            templateUrl: 'src/app/components/steps/registration/user-details.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&',
                completeForm: '&'
            }
        });
})();


(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, $window, api, dealerData, bookingData) {

                var self = this;
                self.dealerData = dealerData;
                self.bookingData = bookingData;

                self.$onInit = function() {
                    _makeWidgetRootScrollable();
                };

                self.makeBooking = function () {
                    self.bookingData.user.firstName = $scope.firstName;
                    self.bookingData.user.lastName = $scope.secondName;
                    self.bookingData.user.email = $scope.email;
                    self.bookingData.user.phone = $scope.phone;
                    self.bookingData.user.comment = $scope.comment;

                    api.completeBooking(self.bookingData).then(function () {
                    });
                    // TODO: add reaction on promise
                    self.completeForm();
                };

                var _makeWidgetRootScrollable = function () {
                    var div = $window.document.getElementsByClassName('test-drive-widget__root')[0];
                    if (div) {
                        div.style.display = 'table';
                    }
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


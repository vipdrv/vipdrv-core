(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, $window, api, dealerData, bookingData) {
                var self = this;

                self.dealerData = dealerData;
                self.bookingData = bookingData;
                self.isLoading = false;

                self.$onInit = function () {
                    _makeWidgetRootScrollable();
                };

                self.makeBooking = function () {
                    self.bookingData.user.firstName = $scope.firstName;
                    self.bookingData.user.lastName = $scope.secondName;
                    self.bookingData.user.email = $scope.email;
                    self.bookingData.user.phone = self.filterPhoneNumber($scope.phone);
                    self.bookingData.user.comment = $scope.comment;
                    self.isLoading = true;
                    api.completeBooking(self.bookingData).then(function () {
                        self.isLoading = false;
                        self.completeForm();
                    });
                };

                var _makeWidgetRootScrollable = function () {
                    var div = $window.document.getElementsByClassName('test-drive-widget__root')[0];
                    if (div) {
                        div.style.display = 'table';
                    }
                };

                /* Helpers */
                document.getElementById('userPhone').addEventListener('input', function (element) {
                    var x = element.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    element.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                });

                self.filterPhoneNumber = function (phoneNumber) {
                    // WARNING: default country code is USA
                    var defaultCuntryCode = '+1';
                    return defaultCuntryCode + phoneNumber.replace(/\D+/g, "");
                }
            },
            templateUrl: 'src/app/components/steps/registration/user-details.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&',
                completeForm: '&'
            }
        });
})();


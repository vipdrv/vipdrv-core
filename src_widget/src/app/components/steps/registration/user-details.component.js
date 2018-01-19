(function () {
    angular.module('myApp')
        .component('tdUserDetails', {
            controller: function ($scope, $window, api, dealerData, bookingData) {
                var self = this;

                self.dealerData = dealerData;
                self.bookingData = bookingData;

                self.$onInit = function () {
                    _makeWidgetRootScrollable();
                    $window.$("#userPhone").intlTelInput({
                        // allowDropdown: false,
                        // autoHideDialCode: false,
                        // autoPlaceholder: "off",
                        // dropdownContainer: "body",
                        // excludeCountries: ["us"],
                        // formatOnDisplay: false,
                        geoIpLookup: function (callback) {
                            $window.$.get("http://ipinfo.io", function () {
                            }, "jsonp").always(function (resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "";
                                callback(countryCode);
                            });
                        },
                        // hiddenInput: "full_number",
                        initialCountry: "auto",
                        // nationalMode: false,
                        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                        // placeholderNumberType: "MOBILE",
                        // preferredCountries: ['cn', 'jp'],
                        // separateDialCode: true,
                        // utilsScript: "build/js/utils.js"
                    });
                };

                self.makeBooking = function () {
                    self.bookingData.user.firstName = $scope.firstName;
                    self.bookingData.user.lastName = $scope.secondName;
                    self.bookingData.user.email = $scope.email;
                    self.bookingData.user.phone = $window.$("#userPhone").intlTelInput("getNumber");
                    self.bookingData.user.comment = $scope.comment;
                    api.completeBooking(self.bookingData).then();
                    self.completeForm();
                };

                var _makeWidgetRootScrollable = function () {
                    var div = $window.document.getElementsByClassName('test-drive-widget__root')[0];
                    if (div) {
                        div.style.display = 'table';
                    }
                };

                /* Methods */

                /* Validation */
                document.getElementById('userPhone').addEventListener('input', function (element) {
                    phoneNumberImputMask(element);
                });

                var userPhone = $window.$("#userPhone");
                var userPhoneSelector = $window.document.getElementById('userPhone');
                userPhone.on("keyup change", function () {
                    if (userPhone.val().length == 14) {
                        userPhoneSelector.style.border = '1px solid #28a745';
                    } else {
                        // userPhoneSelector.style.border = '1px solid #dc3545';
                    }
                });

            },
            templateUrl: 'src/app/components/steps/registration/user-details.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&',
                completeForm: '&'
            }
        });
})();


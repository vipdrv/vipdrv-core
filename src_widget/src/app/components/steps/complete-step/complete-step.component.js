(function () {

    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function ($timeout, $window, api, dealerData, bookingData) {

                var self = this;

                self.dealerData = dealerData;
                self.bookingData = bookingData;
                self.isPageLoaded = false;

                // =======================================================================//
                // Calendar Event                                                         //
                // =======================================================================//

                self.$onInit = function () {
                    _initCalendarButton();
                };

                var _initCalendarButton = function () {
                    var my_awesome_script = $window.document.createElement('script');
                    my_awesome_script.setAttribute('src', 'https://addevent.com/libs/atc/1.6.1/atc.min.js');

                    $window.document.head.appendChild(my_awesome_script);

                    $window.addeventasync = function () {
                        addeventatc.settings({
                            appleical: {show: true, text: "Apple Calendar"},
                            google: {show: true, text: "Google <em>(online)</em>"},
                            outlook: {show: false, text: "Outlook"},
                            outlookcom: {show: true, text: "Outlook.com <em>(online)</em>"},
                            yahoo: {show: false, text: "Yahoo <em>(online)</em>"}
                        });
                    };

                    $timeout(function () {
                        self.isPageLoaded = true;
                    }, 100);
                };

                // =======================================================================//
                // SMS Sending                                                            //
                // =======================================================================//

                self.isSmsSended = false;
                self.isSmsLoading = false;

                self.sendSms = function () {
                    self.isSmsLoading = true;
                    api.sendMeSms(self.bookingData, self.dealerData).then(function () {
                        self.isSmsSended = true;
                        self.isSmsLoading = false;
                    });
                }
            },
            templateUrl: 'src/app/components/steps/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                siteData: '<'
            }
        });
})();


(function () {

    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function ($timeout, $window, api, dealerData, bookingData) {

                var self = this;
                self.dealerData = dealerData;
                self.bookingData = bookingData;

                // =======================================================================//
                // Calendar Event                                                         //
                // =======================================================================//

                self.$onInit = function () {
                    _initCalendarButton();
                    // #warning: CSS hack
                    var div = $window.document.getElementsByClassName('test-drive-widget__root')[0];
                    if (div) {
                        div.style.display = 'table';
                    }
                };

                var _initCalendarButton = function () {
                    var my_awesome_script = $window.document.createElement('script');
                    my_awesome_script.setAttribute('src', 'https://addevent.com/libs/atc/1.6.1/atc.min.js');

                    $window.document.head.appendChild(my_awesome_script);

                    $window.addeventasync = function () {
                        addeventatc.settings({
                            appleical: {show: false, text: "Apple Calendar"},
                            google: {show: true, text: "Google <em>(online)</em>"},
                            outlook: {show: true, text: "Outlook"},
                            outlookcom: {show: true, text: "Outlook.com <em>(online)</em>"},
                            yahoo: {show: false, text: "Yahoo <em>(online)</em>"}
                        });
                    };
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


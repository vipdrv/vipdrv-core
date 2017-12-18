(function () {

    angular.module('myApp')
        .filter('usaDateFormat', function () {
            return function (dateStr) {
                dateStr = dateStr || '';
                var chunks = dateStr.split("-");

                var out = chunks[1] + '/' + chunks[2] + '/' + chunks[0].slice(-2);

                return out;
            };
        });

    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function ($timeout, $window, api) {
                var self = this;

                // =======================================================================//
                // Calendar Event                                                         //
                // =======================================================================//

                self.$onInit = function () {
                    var my_awesome_script = $window.document.createElement('script');
                    my_awesome_script.setAttribute('src', 'https://addevent.com/libs/atc/1.6.1/atc.min.js');

                    $window.document.head.appendChild(my_awesome_script);

                    $window.addeventasync = function () {
                        addeventatc.settings({
                            appleical: {show: true, text: "Apple Calendar"},
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

                    api.sendMeSms(self.userData).then(function () {
                        self.isSmsSended = true;
                        self.isSmsLoading = false;
                    });
                }
            },
            templateUrl: 'src/app/components/steps/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<',
                siteData: '<'
            }
        });
})();


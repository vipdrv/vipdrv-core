(function () {
    angular.module('myApp')
        .component('tdCompleteStep', {
            controller: function () {
                var self = this;

                self.startDate = '2017-12-18 11:00';
                self.endDate = '2017-12-18 13:00';
                self.eventTitle = 'VIP Test Drive';
                self.eventDescription = 'Description of the event';



            },
            templateUrl: 'src/app/components/steps/complete-step/complete-step.tpl.html',
            bindings: {
                car: '<',
                userData: '<'
            }
        });
})();


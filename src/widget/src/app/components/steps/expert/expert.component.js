(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope, dealerData, bookingData) {

                var self = this;
                self.dealerData = dealerData;
                self.bookingData = bookingData;
                self.isStepValid = false;

                self.$onInit = function () {
                    self.validateStep();
                };

                self.expertChanged = function ($event, id, img, name, description) {
                    var index = $event.target.className.indexOf('ngTruncateToggleText');
                    if (index > -1) {
                        return;
                    }

                    self.bookingData.expert.id = id;
                    self.bookingData.expert.img = img;
                    self.bookingData.expert.name = name;
                    self.bookingData.expert.description = description;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.bookingData.expert.name === null) {
                        this.isStepValid = false;
                    } else {
                        this.isStepValid = true;
                    }
                };

                $scope.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                        self.bookingData.calendar.isSkipped = false;
                    }
                };

                $scope.skipStep = function () {
                    self.bookingData.expert.name = 'Skipped';
                    self.bookingData.calendar.isSkipped = true;
                    self.completeStep({tabId: self.tabId});
                };

                self.isAvaliable = function(expertWorkingHours) {
                    if (self.bookingData.calendar.isSkipped) {
                        return true;
                    }

                    var workingHours = [];

                    for(var key in expertWorkingHours) {
                        var day = expertWorkingHours[key];
                        workingHours[day.dayOfWeek] = {
                            startTime: day.startTime,
                            endTime: day.endTime
                        }
                    }

                    var selectedDay = workingHours[self.bookingData.calendar.dayOfWeek];
                    var selectedTime = self.bookingData.calendar.time;

                    if (selectedDay != null) {
                        var hours = Number(selectedTime.match(/^(\d+)/)[1]);
                        var AMPM = selectedTime.match(/\s(.*)$/)[1];

                        if(AMPM.toLowerCase() == "pm" && hours<12) {
                            hours = hours+12;
                        }
                        if(AMPM.toLowerCase() == "am" && hours==12) {
                            hours = hours-12;
                        }

                        var selectedHour = hours.toString();

                        var startTime = parseInt(selectedDay.startTime.split(':')[0]);
                        var endTime = parseInt(selectedDay.endTime.split(':')[0]);

                        if (startTime <= selectedHour && selectedHour <= endTime) {
                            return true;
                        }
                    }

                    return false;
                };
            },
            templateUrl: 'src/app/components/steps/expert/expert.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&'
            }
        });
})();


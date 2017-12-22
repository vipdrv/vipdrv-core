(function () {
    angular.module('myApp')
        .component('tdExpert', {
            controller: function ($scope, dealerData, userData) {

                var self = this;
                self.dealerData = dealerData;
                self.userData = userData;
                self.isStepValid = false;
                self.isNoSalesPersonAvaliable = true;

                self.$onInit = function () {
                    self.validateStep();
                };

                self.expertChanged = function (id, img, name, description) {
                    self.userData.expert.id = id;
                    self.userData.expert.img = img;
                    self.userData.expert.name = name;
                    self.userData.expert.description = description;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.userData.expert.name === null) {
                        this.isStepValid = false;
                    } else {
                        this.isStepValid = true;
                    }
                };

                $scope.nextStep = function () {
                    self.validateStep();
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                        self.userData.calendar.isSkipped = false;
                    }
                };

                $scope.skipStep = function () {
                    self.userData.expert.name = 'Skipped';
                    self.userData.calendar.isSkipped = true;
                    self.completeStep({tabId: self.tabId});
                };

                self.isAvaliable = function(expertWorkingHours) {
                    if (self.userData.calendar.isSkipped) {
                        self.isNoSalesPersonAvaliable = false;
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

                    var selectedDay = workingHours[self.userData.calendar.dayOfWeek];
                    var selectedTime = self.userData.calendar.time;

                    if (selectedDay != null) {
                        var selectedHour = moment(selectedTime, 'HH:mm A').get('hour');
                        var startTime = parseInt(selectedDay.startTime.split(':')[0]);
                        var endTime = parseInt(selectedDay.endTime.split(':')[0]);

                        if (startTime <= selectedHour && selectedHour <= endTime) {
                            self.isNoSalesPersonAvaliable = false;
                            return true;
                        }
                    }

                    return false;
                }

            },
            templateUrl: 'src/app/components/steps/expert/expert.tpl.html',
            bindings: {
                tabId: '<',
                completeStep: '&'
            }
        });
})();


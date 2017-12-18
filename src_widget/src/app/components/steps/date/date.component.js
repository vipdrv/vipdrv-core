(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope) {

                // =======================================================================//
                // Variables                                                              //
                // =======================================================================//

                var self = this;

                self.isStepValid = false;
                self.minDateString = moment().subtract(0, 'day').format('YYYY-MM-DD');
                self.timeIntervals = [];
                self.mobileDateTimeInput= null;
                self.widgetWorkingHours = null;
                self.stepDataDidLoaded = false;
                self.isLoading = true;

                // =======================================================================//
                // Init                                                                   //
                // =======================================================================//

                self.$onInit = function () {
                    self.dateImput = self.userData.calendar.date;
                    self.mobileDateTimeInput = self.userData.calendar.date;
                    self.validateStep();
                };

                self.$onChanges = function ({stepData}) {
                    if (angular.isDefined(stepData)) {
                        if (!stepData.isFirstChange()) {
                            self.isLoading = false;
                            self.stepDataDidLoaded = true;
                            self.widgetWorkingHours = self.mapToWidgetWorkingHours(self.stepData.workingHours);

                            var currentDayOfWeek = new Date().getDay();
                            var startTime = self.widgetWorkingHours[currentDayOfWeek].startTime;
                            var endTime = self.widgetWorkingHours[currentDayOfWeek].endTime;
                            self.timeIntervals = self.stplitTimeToInvervals(startTime, endTime);

                            self.isSelectable = function (date, type) {
                                var dayOfWeek = date.format('d');

                                return self.widgetWorkingHours[dayOfWeek].isActive;
                            };

                            self.isSelectableMobile = function (date, type) {
                                var dayOfWeek = date.format('d');

                                return self.widgetWorkingHours[dayOfWeek].isActive;
                            };
                        }
                    }
                };

                self.mapToWidgetWorkingHours = function (siteWorkingHours) {
                    var defaultWorkingHours = {
                        0: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        1: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        2: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        3: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        4: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        5: {startTime: '09:00:00', endTime: '18:00:00', isActive: false},
                        6: {startTime: '09:00:00', endTime: '18:00:00', isActive: false}
                    };

                    for (var key in siteWorkingHours) {
                        var day = siteWorkingHours[key];

                        defaultWorkingHours[day.dayOfWeek].isActive = true;
                        defaultWorkingHours[day.dayOfWeek].startTime = day.startTime;
                        defaultWorkingHours[day.dayOfWeek].endTime = day.endTime;
                    }

                    return defaultWorkingHours;
                };

                self.dateChanged = function (oldValue, newValue) {
                    var arr = newValue._i.split(' ');

                    self.userData.calendar.date = arr[0];
                    var dayOfWeek = arr[1];
                    self.validateStep();

                    var startTime = self.widgetWorkingHours[dayOfWeek].startTime;
                    var endTime = self.widgetWorkingHours[dayOfWeek].endTime;

                    self.timeIntervals = self.stplitTimeToInvervals(startTime, endTime);
                };

                self.mobileDateChanged = function(oldValue, newValue) {
                    var arr = newValue._i.split(' ');

                    var date = arr[0];
                    var hours = arr[1].split(' ')[0].split(':')[0];
                    var amPm = arr[2];

                    self.userData.calendar.date = date;
                    self.userData.calendar.time = hours + ':' + '00 ' + amPm;

                    self.validateStep();
                };

                self.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.userData.calendar.date != null && self.userData.calendar.time != null) {
                        self.isStepValid = true;
                    } else {
                        self.isStepValid = false;
                    }
                };

                // =======================================================================//
                // Navigation                                                             //
                // =======================================================================//

                $scope.nextStep = function () {
                    if (self.isStepValid) {
                        self.completeStep({tabId: self.tabId});
                    }
                };

                $scope.skipStep = function () {
                    self.completeStep({tabId: self.tabId});
                };

                // =======================================================================//
                // Helpers                                                                //
                // =======================================================================//

                self.stplitTimeToInvervals = function (startTime, endTime) {
                    var start = moment('2000-01-01 ' + startTime);
                    var end = moment('2000-01-01 ' + endTime);
                    var timeIntervalsArr = [];

                    while (start <= end) {
                        timeIntervalsArr.push(start.format('LT'));
                        start.add(1, 'hours');
                    }

                    return timeIntervalsArr;
                };

                self.hoursAsMiliseconds = function (hh_mm) {
                    var arr = hh_mm.split(':');
                    var hours = arr[0];
                    var minutes = arr[1];

                    return new Date('2000', '01', '01', hours, minutes).getTime();
                };

                self.cropSeconds = function (hh_mm_ss) {
                    var arr = hh_mm_ss.split(':');
                    return arr[0] + ':' + arr[1];
                };

                self.extractWorkingHoursFormExperts = function (experts) {
                    for (var key in experts) {
                        var expert = experts[key];

                        if (expert.isActive == true) {
                            for (var key in expert.workingHours) {
                                var expertWorkingHours = expert.workingHours[key];
                                var dayOfWeek = expertWorkingHours.dayOfWeek;
                                self.widgetWorkingHours[dayOfWeek].isActive = true;

                                var expertStartTime = self.hoursAsMiliseconds(expertWorkingHours.startTime);
                                var expertEndTime = self.hoursAsMiliseconds(expertWorkingHours.endTime);

                                var widgetStartTime = self.hoursAsMiliseconds(self.widgetWorkingHours[dayOfWeek].startTime);
                                var widgetEndTime = self.hoursAsMiliseconds(self.widgetWorkingHours[dayOfWeek].endTime);

                                if (expertStartTime < widgetStartTime) {
                                    self.widgetWorkingHours[dayOfWeek].startTime = self.cropSeconds(expertWorkingHours.startTime);
                                }

                                if (expertEndTime > widgetEndTime) {
                                    self.widgetWorkingHours[dayOfWeek].endTime = self.cropSeconds(expertWorkingHours.endTime);
                                }
                            }
                        }
                    }
                };

            },
            templateUrl: 'src/app/components/steps/date/date.tpl.html',
            bindings: {
                userData: '=',
                stepData: '<',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


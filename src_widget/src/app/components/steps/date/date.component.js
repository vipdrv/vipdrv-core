(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope) {

                // =======================================================================//
                // Variables                                                              //
                // =======================================================================//

                var self = this;
                self.isStepValid = false;
                self.isLoading = true;
                self.minDateString = null;
                self.timeIntervals = [];
                self.dateImput = null;
                self.mobileDateTimeInput = null;
                self.widgetWorkingHours = null;
                self.isSelectable = null;
                self.isSelectableMobile = null;

                // =======================================================================//
                // Init                                                                   //
                // =======================================================================//

                self.$onInit = function () {
                    self.minDateString = moment().subtract(0, 'day').format('YYYY-MM-DD');
                    self.dateImput = self.userData.calendar.date;
                    self.mobileDateTimeInput = self.userData.calendar.date;
                    self.validateStep();

                    if (self.stepData && self.stepData.id != null) {
                        self.isLoading = false;
                        self.initStep();
                    }
                };

                self.$onChanges = function ({stepData}) {
                    if (angular.isDefined(stepData)) {
                        if (!stepData.isFirstChange()) {
                            self.isLoading = false;
                            self.initStep();
                        }
                    }
                };

                self.initStep = function () {
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
                        var selectedDayOfWeek = date.format('d');
                        var isDayAvalibale = self.widgetWorkingHours[selectedDayOfWeek].isActive;

                        var isTimeAvalibale = false;
                        var selectedHour =  date.hours();
                        var startTime = self.widgetWorkingHours[selectedDayOfWeek].startTime.split(':')[0];
                        var endTime = self.widgetWorkingHours[selectedDayOfWeek].endTime.split(':')[0];

                        if (startTime <= selectedHour && selectedHour <= endTime) {
                            var isTimeAvalibale = true;
                        }

                        return isDayAvalibale && isTimeAvalibale;
                    };
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
                    var dayOfWeek = arr[1];

                    self.userData.calendar.time = cleatTimeIfInvalid(self.userData.calendar.time, dayOfWeek);
                    self.userData.calendar.date = arr[0];
                    self.userData.calendar.dayOfWeek = dayOfWeek;
                    self.validateStep();

                    var startTime = self.widgetWorkingHours[dayOfWeek].startTime;
                    var endTime = self.widgetWorkingHours[dayOfWeek].endTime;

                    self.timeIntervals = self.stplitTimeToInvervals(startTime, endTime);
                };

                function cleatTimeIfInvalid(selectedTime, dayOfWeek) {
                    if (!selectedTime) {
                        return selectedTime;
                    }

                    var selectedHour = moment(selectedTime, 'HH:mm A').get('hour');
                    var startTime = parseInt(self.widgetWorkingHours[dayOfWeek].startTime.split(':')[0]);
                    var endTime = parseInt(self.widgetWorkingHours[dayOfWeek].endTime.split(':')[0]);

                    if (!(startTime <= selectedHour && selectedHour <= endTime)) {
                        return null;
                    }
                    return selectedTime;
                }

                self.mobileDateChanged = function (oldValue, newValue) {
                    var arr = newValue._i.split(' ');

                    var date = arr[0];
                    var hours = arr[1].split(' ')[0].split(':')[0];
                    var amPm = arr[2];

                    self.userData.calendar.date = date;
                    self.userData.calendar.time = hours + ':' + '00 ' + amPm;
                    self.userData.calendar.isSkipped = false;
                    self.validateStep();
                };

                self.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                    self.userData.calendar.isSkipped = false;
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
                    self.userData.calendar.isSkipped = true;
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


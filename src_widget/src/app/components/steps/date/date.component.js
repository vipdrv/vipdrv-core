(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function ($scope, $interval, dealerData, bookingData) {

                // =======================================================================//
                // Variables                                                              //
                // =======================================================================//

                var self = this;
                self.dealerData = dealerData;
                self.bookingData = bookingData;
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

                self.stop;

                self.$onInit = function () {
                    self.minDateString = moment().subtract(0, 'day').format('YYYY-MM-DD');
                    self.dateImput = self.bookingData.calendar.date;
                    self.mobileDateTimeInput = self.bookingData.calendar.date;
                    self.validateStep();

                    if (self.dealerData.siteId != null) {
                        self.isLoading = false;
                        self.initStep();
                    } else {
                        self.isLoading = true;
                        self.stop = $interval(function () {
                            if (self.dealerData.siteId == null) {
                                // console.log('loading');
                            } else {
                                self.isLoading = false;
                                self.stopInterval();
                                self.initStep();
                            }
                        }, 100);
                    }
                };

                self.stopInterval = function() {
                    if (angular.isDefined(self.stop)) {
                        $interval.cancel(self.stop);
                        self.stop = undefined;
                    }
                };

                // =======================================================================//
                // Step Logic                                                             //
                // =======================================================================//

                self.initStep = function () {
                    self.widgetWorkingHours = self.mapToWidgetWorkingHours(self.dealerData.workingHours);

                    var currentDayOfWeek = new Date().getDay();
                    var startTime = self.widgetWorkingHours[currentDayOfWeek].startTime;
                    var endTime = self.widgetWorkingHours[currentDayOfWeek].endTime;

                    self.timeIntervals = self.splitTimeToInvervals(startTime, endTime);

                    self.isSelectable = function (date, type) {
                        var dayOfWeek = date.format('d');
                        return self.widgetWorkingHours[dayOfWeek].isActive;
                    };

                    self.isSelectableMobile = function (date, type) {
                        var selectedDayOfWeek = date.format('d');
                        var isDayAvalibale = self.widgetWorkingHours[selectedDayOfWeek].isActive;

                        var isTimeAvalibale = false;
                        var selectedHour = date.hour();

                        var startTime = self.widgetWorkingHours[selectedDayOfWeek].startTime.split(':')[0];
                        var endTime = self.widgetWorkingHours[selectedDayOfWeek].endTime.split(':')[0];

                        if (startTime <= selectedHour && selectedHour <= endTime) {
                            // TODO: Dispel magic! (╯°□°）╯︵ ┻━┻)
                            isTimeAvalibale = true;
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

                    self.bookingData.calendar.time = cleanTimeIfInvalid(self.bookingData.calendar.time, dayOfWeek);
                    self.bookingData.calendar.date = arr[0];
                    self.bookingData.calendar.dayOfWeek = dayOfWeek;
                    self.validateStep();

                    var startTime = self.widgetWorkingHours[dayOfWeek].startTime;
                    var endTime = self.widgetWorkingHours[dayOfWeek].endTime;

                    self.timeIntervals = self.splitTimeToInvervals(startTime, endTime);
                };

                function cleanTimeIfInvalid(selectedTime, dayOfWeek) {
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
                    var dayOfWeek = arr[3];

                    self.bookingData.calendar.date = date;
                    self.bookingData.calendar.time = hours + ':' + '00 ' + amPm;
                    self.bookingData.calendar.dayOfWeek = dayOfWeek;
                    self.bookingData.calendar.isSkipped = false;
                    self.validateStep();
                };

                self.timeChanged = function (time) {
                    self.bookingData.calendar.time = time;
                    self.bookingData.calendar.isSkipped = false;
                    self.validateStep();
                };

                self.validateStep = function () {
                    if (self.bookingData.calendar.date != null && self.bookingData.calendar.time != null) {
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
                    self.bookingData.calendar.isSkipped = true;
                    self.completeStep({tabId: self.tabId});
                };

                // =======================================================================//
                // Helpers                                                                //
                // =======================================================================//

                self.splitTimeToInvervals = function (startTime, endTime) {
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
                tabId: '<',
                completeStep: '&'
            }
        });
})();


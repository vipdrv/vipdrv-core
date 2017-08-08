(function () {
    angular.module('myApp')
        .component('tdDate', {
            controller: function (api) {
                var self = this;
                var now = new Date();
                var tomorrow = new Date();
                var max = new Date();
                max.setDate(now.getDate() + 30);
                tomorrow.setDate(now.getDate() + 1);
                self.minDate =  now;
                self.maxDate =  max;
                self.myDate = tomorrow;

                self.dateFilter =  _dateFilter;

                this.timeIntervals = [];


                this.isSatisfy = null;

                this.$onInit = function () {
                    console.log('on init td-tdDate');
                    console.log('myDate', self.myDate);
                    if (self.userData.calendar.date === null || self.userData.calendar.time === null) {
                        this.isSatisfy = false;
                    }

                    api.loadOpenHours().then(openHoursLoaded);
                };

                function openHoursLoaded(json) {
                    console.log(json);
                    self.openHours = json.open_hours.days;
                    // find nearest available date
                    while (!_dateFilter(self.myDate) && self.myDate < self.maxDate){
                        self.myDate.setDate(self.myDate.getDate() + 1);
                    }

                    self.dateChanged(self.myDate);
                    self.userData.calendar.date = formatDate(self.myDate);


                }


                this.timeChanged = function (time) {
                    self.userData.calendar.time = time;
                    self.satisfyStep();
                };

                function _dateFilter(date) {
                    var day = date.getDay();
                    var isOpen = self.openHours[day].is_open;
                    return isOpen;
                }

                 function split(str) {
                     var split = str.split(':');
                    var x = {
                        hours: split[0],
                        minutes: split[1]
                    };
                    return x;
                }
                 function getIntervalWithinDay(myDate) {
                    var timeintervals = self.openHours[myDate.getDay()];

                    var x = split(timeintervals.from);
                    var from = new Date(self.myDate.valueOf());
                    from.setHours(x.hours, x.minutes);

                    var to = new Date(self.myDate.valueOf());
                    var y = split(timeintervals.to);
                    to.setHours(y.hours, y.minutes);

                    var intervalWithinDay = {
                        from: from,
                        to: to
                    };

                     // console.log(intervalWithinDay);

                    return intervalWithinDay;
                }

                function updateTimeOptions(indayInterval) {
                    var options = [];
                    var x = new Date(indayInterval.from.valueOf());
                    while (x  < indayInterval.to) {
                        var minutes = x.getMinutes();
                        if (minutes < 10) {
                            minutes = '0' + minutes;
                        }

                        var items = x.getHours() + ':' + minutes;

                        options.push(items);

                        x.setMinutes(x.getMinutes() + 30);
                    }


                    self.timeIntervals = options;
                }

                this.dateChanged = function () {
                    self.userData.calendar.date = formatDate(self.myDate);

                    var myDate = self.myDate;
                    var indayInterval =  getIntervalWithinDay(myDate);
                    updateTimeOptions(indayInterval);

                    self.userData.calendar.time = null
                    self.satisfyStep();
                };

                this.satisfyStep = function () {
                    if (self.userData.calendar.date === null || self.userData.calendar.time === null) {
                        this.isSatisfy = false;
                    } else {
                        this.isSatisfy = true;
                    }
                };

                this.completeStepInner = function () {
                    if (self.isSatisfy) {
                        self.completeStep({tabId: this.tabId});
                    }
                };

                var formatDate = function (date) {
                    var d = new Date(date),
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();

                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;

                    return [year, month, day].join('/');
                }
            },
            templateUrl: 'src/app/components/steps/date/date.tpl.html',
            bindings: {
                userData: '=',
                tabId: '<',
                completeStep: '&'
            }
        });
})();


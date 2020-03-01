(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {

            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            this.retrieveSite = function () {
                var req = {
                    method: 'GET',
                    url: apiBaseUrl + '/site/' + siteId + '/aggregated-info',
                    headers: {
                        'content-type': 'application/json'
                    }
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            this.completeBooking = function (bookingData) {
                var data = JSON.stringify(mapToBookingRequestDto(bookingData));

                var req = {
                    method: 'POST',
                    url: apiBaseUrl + "/lead/" + siteId + "/complete-booking",
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: data
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            this.sendMeSms = function (bookingData, dealerData) {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + '/lead/' + siteId + '/send-sms',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: JSON.stringify(mapToSmsDto(bookingData, dealerData))
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            var mapToSmsDto = function (bookingData, dealerData) {

                var smsDto = {
                    phone: null,
                    bookingDateTimeUtc: null,
                    timeZoneOffset: null,
                    vehicleTitle: null,
                    expertName: null,
                    beverageName: null,
                    roadName: null,
                    dealerName: null,
                    dealerPhone: null
                };

                if (bookingData.calendar.date && bookingData.calendar.time) {
                    var date = new Date(bookingData.calendar.date);
                    var time = bookingData.calendar.time;

                    var hours = Number(time.match(/^(\d+)/)[1]);
                    var minutes = Number(time.match(/:(\d+)/)[1]);
                    var AMPM = time.match(/\s(.*)$/)[1];

                    if(AMPM.toLowerCase() == "pm" && hours<12) {
                        hours = hours+12;
                    }
                    if(AMPM.toLowerCase() == "am" && hours==12) {
                        hours = hours-12;
                    }

                    var sHours = hours.toString();
                    var sMinutes = minutes.toString();
                    if(hours<10) sHours = "0" + sHours;
                    if(minutes<10) sMinutes = "0" + sMinutes;

                    date.setHours(sHours);
                    date.setMinutes(sMinutes);

                    smsDto.bookingDateTimeUtc = date;
                }
                var date = new Date();
                smsDto.timeZoneOffset = date.getTimezoneOffset();

                smsDto.phone = bookingData.user.phone || null;
                smsDto.vehicleTitle = bookingData.vehicle.title || "Not specified";
                smsDto.expertName = bookingData.expert.name || "Skipped by customer";
                smsDto.beverageName = bookingData.beverage.name || "Skipped by customer";
                smsDto.roadName = bookingData.road.name || "Skipped by customer";
                smsDto.dealerName = dealerData.name || "";
                smsDto.dealerPhone = dealerData.phone || "";

                return smsDto;
            };

            var mapToBookingRequestDto = function (bookingData) {

                var bookingDto = {
                    timeZoneOffset: null,
                    bookingUser: {
                        firstName: null,
                        lastName: null,
                        phone: null,
                        email: null,
                        comment: null
                    },
                    bookingDateTimeUtc: null,
                    bookingVehicle: {
                        vin: null,
                        stock: null,
                        year: null,
                        make: null,
                        model: null,
                        body: null,
                        title: null,
                        engine: null,
                        exterior: null,
                        interior: null,
                        drivetrain: null,
                        transmission: null,
                        msrp: null,
                        imageUrl: null,
                        vdpUrl: null

                    },
                    expertId: null,
                    beverageId: null,
                    roadId: null
                };

                bookingDto.bookingUser.firstName = bookingData.user.firstName || null;
                bookingDto.bookingUser.lastName = bookingData.user.lastName || null;
                bookingDto.bookingUser.phone = bookingData.user.phone || null;
                bookingDto.bookingUser.email = bookingData.user.email || null;
                bookingDto.bookingUser.comment = bookingData.user.comment || null;

                if (bookingData.calendar.date && bookingData.calendar.time) {
                    var date = new Date();

                    var dateChunks = bookingData.calendar.date.split("-");
                    var mounth = dateChunks[1] - 1;
                    var day = dateChunks[2];
                    date.setMonth(mounth);
                    date.setDate(day);

                    var time = bookingData.calendar.time;
                    var hours = Number(time.match(/^(\d+)/)[1]);
                    var minutes = Number(time.match(/:(\d+)/)[1]);
                    var AMPM = time.match(/\s(.*)$/)[1];
                    if(AMPM.toLowerCase() == "pm" && hours<12) {
                        hours = hours+12;
                    }
                    if(AMPM.toLowerCase() == "am" && hours==12) {
                        hours = hours-12;
                    }

                    var sHours = hours.toString();
                    var sMinutes = minutes.toString();
                    if(hours<10) sHours = "0" + sHours;
                    if(minutes<10) sMinutes = "0" + sMinutes;

                    date.setHours(sHours);
                    date.setMinutes(sMinutes);

                    bookingDto.bookingDateTimeUtc = date;
                }
                var date = new Date();
                bookingDto.timeZoneOffset = date.getTimezoneOffset();

                bookingDto.bookingVehicle.vin = bookingData.vehicle.vin || null;
                bookingDto.bookingVehicle.stock = bookingData.vehicle.stock || null;
                bookingDto.bookingVehicle.year = bookingData.vehicle.year || null;
                bookingDto.bookingVehicle.make = bookingData.vehicle.make || null;
                bookingDto.bookingVehicle.model = bookingData.vehicle.model || null;
                bookingDto.bookingVehicle.body = bookingData.vehicle.body || null;
                bookingDto.bookingVehicle.title = bookingData.vehicle.title || null;
                bookingDto.bookingVehicle.engine = bookingData.vehicle.engine || null;
                bookingDto.bookingVehicle.exterior = bookingData.vehicle.exterior || null;
                bookingDto.bookingVehicle.interior = bookingData.vehicle.interior || null;
                bookingDto.bookingVehicle.drivetrain = bookingData.vehicle.drivetrain || null;
                bookingDto.bookingVehicle.transmission = bookingData.vehicle.transmission || null;
                bookingDto.bookingVehicle.msrp = bookingData.vehicle.msrp || null;
                bookingDto.bookingVehicle.imageUrl = bookingData.vehicle.imageUrl || 'http://widget.testdrive.pw/img/default-car.png';
                bookingDto.bookingVehicle.vdpUrl = bookingData.vehicle.vdpUrl || '#';

                bookingDto.expertId = bookingData.expert.id || null;
                bookingDto.beverageId = bookingData.beverage.id || null;
                bookingDto.roadId = bookingData.road.id || null;

                return bookingDto;
            };
        });
})();
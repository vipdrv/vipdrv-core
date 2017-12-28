(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {

            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            this.retrieveExperts = function () {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + "/expert/get-all",
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: {
                        "siteId": siteId,
                        "sorting": "name asc"
                    }
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            this.retrieveBeverages = function () {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + "/beverage/get-all",
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: {
                        "siteId": siteId,
                        "sorting": "name asc"
                    }
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            this.retrieveRoutes = function () {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + '/route/get-all',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: {
                        "siteId": siteId,
                        "sorting": "name asc"
                    }
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            this.retrieveSite = function () {
                var req = {
                    method: 'GET',
                    url: apiBaseUrl + '/site/' + siteId,
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
                    bookingDateTime: null,
                    vehicleTitle: null,
                    expertName: null,
                    beverageName: null,
                    roadName: null,
                    dealerName: null,
                    dealerPhone: null
                };

                if (bookingData.calendar.date && bookingData.calendar.time) {
                    var dateTime = bookingData.calendar.date + ' ' + bookingData.calendar.time;
                    smsDto.bookingDateTime = moment(dateTime).format('YYYY-MM-DD HH:mm');
                }
                smsDto.phone = bookingData.user.phone || null;
                smsDto.vehicleTitle = bookingData.car.title || "Not specified";
                smsDto.expertName = bookingData.expert.name || "Skipped by customer";
                smsDto.beverageName = bookingData.beverage.name || "Skipped by customer";
                smsDto.roadName = bookingData.road.name || "Skipped by customer";
                smsDto.dealerName = dealerData.name || "";
                smsDto.dealerPhone = dealerData.phone || "";

                return smsDto;
            };

            var mapToBookingRequestDto = function (bookingData) {

                var bookingDto = {
                    bookingUser: {
                        firstName: null,
                        lastName: null,
                        phone: null,
                        email: null,
                        comment: null
                    },
                    bookingDateTime: null,
                    bookingCar: {
                        VIN: null,
                        imageUrl: null,
                        title: null
                    },
                    expertId: null,
                    beverageId: null,
                    roadId: null
                };

                bookingDto.bookingUser.firstName = bookingData.user.firstName || null;
                bookingDto.bookingUser.lastName = bookingData.user.lastName || '';
                bookingDto.bookingUser.phone = bookingData.user.phone || null;
                bookingDto.bookingUser.email = bookingData.user.email || null;
                bookingDto.bookingUser.comment = bookingData.user.comment || '';

                if (bookingData.calendar.date && bookingData.calendar.time) {
                    var dateTime = bookingData.calendar.date + ' ' + bookingData.calendar.time;
                    bookingDto.bookingDateTime = moment(dateTime).format('YYYY-MM-DD HH:mm');
                }

                bookingDto.bookingCar.title = bookingData.car.title || "Not specified";
                bookingDto.bookingCar.VIN = bookingData.car.vin || "Not specified";
                bookingDto.bookingCar.imageUrl = bookingData.car.imageUrl || 'http://widget.testdrive.pw/img/default-car.png';
                bookingDto.bookingCar.vdpUrl = bookingData.car.vdpUrl || '#';

                bookingDto.expertId = bookingData.expert.id || null;
                bookingDto.beverageId = bookingData.beverage.id || null;
                bookingDto.roadId = bookingData.road.id || null;

                return bookingDto;
            };
        });
})();
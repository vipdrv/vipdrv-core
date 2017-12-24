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

            this.completeBooking = function (userData) {
                var data = JSON.stringify(mapToBookingRequestDto(userData));

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

            this.sendMeSms = function (userData, dealerData) {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + '/lead/' + siteId + '/send-sms',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: JSON.stringify(mapToSmsDto(userData, dealerData))
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

            var mapToSmsDto = function (userData, dealerData) {

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

                if (userData.calendar.date && userData.calendar.time) {
                    var dateTime = userData.calendar.date + ' ' + userData.calendar.time;
                    smsDto.bookingDateTime = moment(dateTime).format('YYYY-MM-DD HH:mm');
                }
                smsDto.phone = userData.user.phone || null;
                smsDto.vehicleTitle = userData.car.title || "Not specified";
                smsDto.expertName = userData.expert.name || "Skipped by customer";
                smsDto.beverageName = userData.beverage.name || "Skipped by customer";
                smsDto.roadName = userData.road.name || "Skipped by customer";
                smsDto.dealerName = dealerData.name || "";
                smsDto.dealerPhone = dealerData.name || "";

                return smsDto;
            };

            var mapToBookingRequestDto = function (userData) {

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

                bookingDto.bookingUser.firstName = userData.user.firstName || null;
                bookingDto.bookingUser.lastName = userData.user.lastName || '';
                bookingDto.bookingUser.phone = userData.user.phone || null;
                bookingDto.bookingUser.email = userData.user.email || null;
                bookingDto.bookingUser.comment = userData.user.comment || '';

                if (userData.calendar.date && userData.calendar.time) {
                    var dateTime = userData.calendar.date + ' ' + userData.calendar.time;
                    bookingDto.bookingDateTime = moment(dateTime).format('YYYY-MM-DD HH:mm');
                }

                bookingDto.bookingCar.title = userData.car.title || "Not specified";
                bookingDto.bookingCar.VIN = userData.car.vin || "Not specified";
                bookingDto.bookingCar.imageUrl = userData.car.imageUrl || 'http://widget.testdrive.pw/img/default-car.png';
                bookingDto.bookingCar.vdpUrl = userData.car.vdpUrl || '#';

                bookingDto.expertId = userData.expert.id || null;
                bookingDto.beverageId = userData.beverage.id || null;
                bookingDto.roadId = userData.road.id || null;

                return bookingDto;
            };
        });
})();
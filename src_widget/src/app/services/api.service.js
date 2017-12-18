(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {

            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            this.retrieveOpenHours = function () {
                var req = {
                    method: 'GET',
                    url: apiBaseUrl + '/site/' + siteId + '/week-schedule',
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

            this.sendMeSms = function (userData) {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + '/lead/' + siteId + '/send-sms',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: JSON.stringify(mapToSmsDto(userData))
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


            var mapToSmsDto = function (userData) {

                var smsDto = {
                    phone: null,
                    dateTime: null,
                    vehicleTitle: null,
                    expertTitle: null,
                    beverageTitle: null,
                    roadTitle: null
                };

                smsDto.phone = userData.user.phone;
                smsDto.dateTime = userData.calendar.date + ' ' + userData.calendar.time;
                smsDto.vehicleTitle = userData.car.title;
                smsDto.expertTitle = userData.expert.name;
                smsDto.beverageTitle = userData.beverage.name;
                smsDto.roadTitle = userData.road.name;

                return smsDto;
            };

            var mapToBookingRequestDto = function (userData) {

                var bookingDto = {
                    "bookingUser": {
                        "firstName": null,
                        "lastName": null,
                        "phone": null,
                        "email": null
                    },
                    "bookingDateTime": {
                        "date": null,
                        "time": null
                    },
                    "bookingCar": {
                        "VIN": null,
                        "imageUrl": null,
                        "title": null
                    },
                    "expertId": null,
                    "beverageId": null,
                    "roadId": null
                };

                bookingDto.bookingUser.firstName = userData.user.firstName;
                bookingDto.bookingUser.lastName = userData.user.lastName;
                bookingDto.bookingUser.phone = userData.user.phone;
                bookingDto.bookingUser.email = userData.user.email;

                bookingDto.bookingDateTime.date = userData.calendar.date;
                bookingDto.bookingDateTime.time = userData.calendar.time;

                bookingDto.bookingCar.VIN = "1FTEF25N9RLB80787"; // TODO: Fake VIN
                bookingDto.bookingCar.imageUrl = userData.car.imageUrl;
                bookingDto.bookingCar.title = userData.car.title;

                bookingDto.expertId = userData.expert.id;
                bookingDto.beverageId = userData.beverage.id;
                bookingDto.roadId = userData.road.id;

                return bookingDto;
            };

        });
})();
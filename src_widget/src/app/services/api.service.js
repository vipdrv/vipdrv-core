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

            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            var mapToBookingRequestDto = function (userData) {

                var bookingDto = {
                    "bookingUser": {
                        "firstName": null,
                        "lastName": null,
                        "phone": null,
                        "email": null
                    },
                    "calendar": {
                        "date": null,
                        "time": null
                    },
                    "bookingCar": {
                        "img": null,
                        "title": null,
                        "engine": null,
                        "year": null,
                        "colour": null,
                        "transmission": null,
                        "fuel": null
                    },
                    "expertId": null,
                    "beverageId": null,
                    "roadId": null
                };

                bookingDto.bookingUser = userData.user;
                bookingDto.bookingCar = userData.car;
                bookingDto.expertId = userData.expert.id;
                bookingDto.beverageId = userData.beverage.id;
                bookingDto.roadId = userData.road.id;

                return bookingDto;
            }

        });
})();
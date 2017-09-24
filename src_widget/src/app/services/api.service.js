(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {


            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            this.loadOpenHours = function () {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/open-hours';

                $http.get(url).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {
                    deferred.reject(err);
                });

                return deferred.promise;
            };

            this.retrieveExperts = function () {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + '/expert/get-all',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: {
                        siteId: siteId,
                        sorting: 'order asc'
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
                    url: apiBaseUrl + '/beverage/get-all',
                    headers: {
                        'content-type': 'application/json'
                    },
                    data: {
                        siteId: siteId,
                        sorting: 'order asc'
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
                        siteId: siteId,
                        sorting: 'order asc'
                    }
                };

                var promise = $http(req).then(function (responce) {
                    return responce.data;
                }, function () {
                });

                return promise;
            };

            // =======================================================================//

        });
})();
(function () {
    'use strict';

    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {

            this.submitForm = _submitForm;
            this.loadExperts = _loadExperts;
            this.loadBeverages = _loadBeverages;
            this.loadRoads = _loadRoads;
            this.loadOpenHours = _loadOpenHours;

            function _loadOpenHours() {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/open-hours';

                $http.get(url).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {
                    deferred.reject(err);
                });

                return deferred.promise;
            }

            function _loadRoads() {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/routes';

                $http.get(url).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {
                    deferred.reject(err);
                });

                return deferred.promise;
            }

            function _loadBeverages() {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/beverages';

                $http.get(url).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {

                    deferred.reject(err);
                });

                return deferred.promise;
            }

            function _loadExperts() {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/experts';

                $http.get(url).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {
                    deferred.reject(err);
                });

                return deferred.promise;
            }

            function _submitForm(formData) {
                var deferred = $q.defer();
                var url = apiBaseUrl + '/sites/' + siteId + '/SubmitForm';

                $http.post(url, formData).then(function (response) {
                    var json = response.data;
                    deferred.resolve(json);
                }, function (err) {
                    deferred.reject(err);
                });

                return deferred.promise;
            }
        });
})();
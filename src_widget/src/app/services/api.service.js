(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {


            // =======================================================================//
            // Get Widget Data                                                        //
            // =======================================================================//

            this.retrieveOpenHours = function (callback) {
                var data = null;

                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        callback(JSON.parse(this.responseText));
                    }
                });

                xhr.open("GET", apiBaseUrl + '/site/' + siteId + '/week-schedule');
                xhr.setRequestHeader("cache-control", "no-cache");
                xhr.setRequestHeader("postman-token", "a53be7ab-633b-a65b-3314-1e4494688163");

                xhr.send(data);
            };

            this.retrieveExperts = function (callback) {
                var data = JSON.stringify({
                    "sorting": "name asc"
                });

                var xhr = new XMLHttpRequest();

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        callback(JSON.parse(this.responseText));
                    }
                });

                xhr.open("POST", apiBaseUrl + "/expert/get-all?page=0&pageSize=5");
                xhr.setRequestHeader("content-type", "application/json");
                xhr.setRequestHeader("cache-control", "no-cache");

                xhr.send(data);
            };

            this.retrieveBeverages = function (callback) {
                var data = JSON.stringify({
                    "siteId": siteId,
                    "sorting": "name asc"
                });

                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        callback(JSON.parse(this.responseText));
                    }
                });

                xhr.open("POST", apiBaseUrl + "/beverage/get-all");
                xhr.setRequestHeader("content-type", "application/json");
                xhr.setRequestHeader("cache-control", "no-cache");

                xhr.send(data);
            };

            this.retrieveRoutes = function (callback) {
                var data = JSON.stringify({
                    "siteId": siteId,
                    "sorting": "name asc"
                });

                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        callback(JSON.parse(this.responseText));
                    }
                });

                xhr.open("POST", apiBaseUrl + '/route/get-all');
                xhr.setRequestHeader("content-type", "application/json");
                xhr.setRequestHeader("cache-control", "no-cache");

                xhr.send(data);
            };

            this.completeBooking = function (data, callback) {
                var data = JSON.stringify({
                    "siteId": siteId,
                    "sorting": "name asc"
                });

                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        callback(JSON.parse(this.responseText));
                    }
                });

                xhr.open("POST", apiBaseUrl + '/route/get-all');
                xhr.setRequestHeader("content-type", "application/json");
                xhr.setRequestHeader("cache-control", "no-cache");

                xhr.send(data);
            };

            // =======================================================================//

        });
})();
(function () {
    'use strict';
    angular.module('myApp')
        .service('api', function ($http, $q, apiBaseUrl, siteId) {

            this.loadOpenHours = function() {
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

            this.retrieveExperts = function() {
                var req = {
                    method: 'POST',
                    url: apiBaseUrl + 'https://app14.gunnebocloud.com/smartbusiness.auth/connect/token',
                    headers: {
                        'cache-control': 'no-cache',
                        'content-type': 'application/x-www-form-urlencoded',
                        'authorization': 'Basic ' + helisSecretToken
                    },
                    data: {
                        grant_type: 'password',
                        username: login,
                        password: password,
                        scope: 'SmartBusinessRestApi openid offline_access',
                    }
                };

                req.transformRequest = function (obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                };

                var promise = $http(req).then(function (responce) {
                    if (responce.data.access_token && responce.data.access_token) {
                        self.updateHeliosServerToken(responce.data.access_token, responce.data.refresh_token);
                        return responce.data;
                    }
                    return false;
                }, function () {
                });

                return promise;
            };

        });
})();
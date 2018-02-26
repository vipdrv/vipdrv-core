(function () {
    var app = angular.module('myApp', ['templates', 'moment-picker', 'ngTextTruncate']);

    // =======================================================================//
    // App Configs                                                            //
    // =======================================================================//

    var apiBaseUrl = '%apiBaseUrl%';
    var defaultSiteId = '%siteId%';

    //=======================================================================//
    // Global Variables                                                      //
    //=======================================================================//

    var widgetTabs = {
        time: {
            id: 'time',
            title: 'Select Date & Time',
            icon: 'svg-white-clock-o.svg',
            isActive: true,
            isLocked: false,
            isCompleted: false,
            isUsed: true
        },
        expert: {
            id: 'expert',
            icon: 'svg-white-users.svg',
            title: 'Select Expert',
            isActive: false,
            isLocked: true,
            isCompleted: false,
            isUsed: true
        },
        beverage: {
            id: 'beverage',
            icon: 'svg-white-coffee.svg',
            title: 'Select Beverage',
            isActive: false,
            isLocked: true,
            isCompleted: false,
            isUsed: true
        },
        road: {
            id: 'road',
            icon: 'svg-white-road.svg',
            title: 'Select Preferred Route',
            isActive: false,
            isLocked: true,
            isCompleted: false,
            isUsed: true
        },
        details: {
            id: 'details',
            icon: 'svg-white-handshake-o.svg',
            title: 'Your Details',
            isActive: false,
            isLocked: true,
            isCompleted: false
        }
    };
    var defaultWidgetTabs = JSON.parse(JSON.stringify(widgetTabs)); // hardcore way to copy an object

    var bookingData = {
        user: {
            firstName: null,
            lastName: null,
            phone: null,
            email: null,
            comment: null
        },
        calendar: {
            date: null,
            time: null,
            dayOfWeek: null,
            isSkipped: null
        },
        expert: {
            id: null,
            img: null,
            name: null,
            description: null,
            isSkipped: null
        },
        beverage: {
            id: null,
            img: null,
            name: null,
            description: null,
            isSkipped: null
        },
        road: {
            id: null,
            img: null,
            name: null,
            description: null,
            isSkipped: null
        },
        vehicle: {
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
        closeWidgetFrame: null
    };
    var defaultBookingData = JSON.parse(JSON.stringify(bookingData)); // hardcore way to copy an object

    var dealerData = {
        siteId: null,
        name: null,
        phone: null,
        address: null,
        siteUrl: null,
        workingHours: [],
        experts: {
            isStepEnabled: true,
            stepOrder: 0,
            items: []
        },
        beverages: {
            isStepEnabled: true,
            stepOrder: 1,
            items: []
        },
        roads: {
            isStepEnabled: true,
            stepOrder: 2,
            items: []
        }
    };
    var globalState = {
        isBookingCompleted: false
    };

    app.value('siteId', getParameterByName('siteId') || defaultSiteId);
    app.value('widgetTabs', widgetTabs);
    app.value('defaultWidgetTabs', defaultWidgetTabs);
    app.value('globalState', globalState);
    app.value('bookingData', bookingData);
    app.value('defaultBookingData', defaultBookingData);
    app.value('dealerData', dealerData);
    app.value('apiBaseUrl', apiBaseUrl);

    /**
     * ------------------------------------------------------------------------
     * Init widget theme
     * ------------------------------------------------------------------------
     */

    var widgetTheme = getParameterByName('widgetTheme') || 'blue';
    var link = document.createElement('link');
    link.href = window.location.origin + '/theme-' + widgetTheme + '.css';
    link.type = 'text/css';
    link.rel = 'stylesheet';
    document.getElementsByTagName('head')[0].appendChild(link);

    /**
     * ------------------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------------------
     */

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

})();

(function () {


    if (typeof Object.assign != 'function') {
        Object.assign = function (target) {
            'use strict';
            if (target == null) {
                throw new TypeError('Cannot convert undefined or null to object');
            }

            target = Object(target);
            for (var index = 1; index < arguments.length; index++) {
                var source = arguments[index];
                if (source != null) {
                    for (var key in source) {
                        if (Object.prototype.hasOwnProperty.call(source, key)) {
                            target[key] = source[key];
                        }
                    }
                }
            }
            return target;
        };
    }


})();
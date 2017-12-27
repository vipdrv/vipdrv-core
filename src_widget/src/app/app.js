(function () {
    var app = angular.module('myApp', ['templates', 'moment-picker', 'ngTextTruncate']);

    // =======================================================================//
    // App Configs                                                            //
    // =======================================================================//

    var apiBaseUrl = '%apiBaseUrl%';
    var defaultSiteId = '%siteId%';

    // =======================================================================//
    // Global Variables                                                       //
    // =======================================================================//

    var widgetTabs = {
        time: {
            id: 'time',
            title: 'Select Date & Time',
            icon: 'fa fa-clock-o fa-2x',
            isActive: true,
            isLocked: false,
            isCompleted: false
        },
        expert: {
            id: 'expert',
            icon: 'fa fa-users fa-2x',
            title: 'Select Expert',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        beverage: {
            id: 'beverage',
            icon: 'fa fa-coffee fa-2x',
            title: 'Select Beverage',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        road: {
            id: 'road',
            icon: 'fa fa-road fa-2x',
            title: 'Select Preferred Route',
            isActive: false,
            isLocked: true,
            isCompleted: false
        },
        details: {
            id: 'details',
            icon: 'fa fa-handshake-o fa-2x',
            title: 'Your Details',
            isActive: false,
            isLocked: true,
            isCompleted: false
        }
    };

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
        car: {
            vin: null,
            imageUrl: null,
            vdpUrl: null,
            title: null,
            engine: null,
            year: null,
            colour: null,
            transmission: null,
            fuel: null
        }
    };

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
        isFormCompleted: false
    };

    app.value('widgetTabs', widgetTabs);
    app.value('globalState', globalState);
    app.value('bookingData', bookingData);
    app.value('dealerData', dealerData);
    app.value('apiBaseUrl', apiBaseUrl);

    var url = new FiltersFromUrl(window.location.search).get();
    var siteId = url.site_id || defaultSiteId;
    app.value('siteId', siteId);
})();

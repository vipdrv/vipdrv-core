//HEAD 
(function(app) {
try { app = angular.module("templates"); }
catch(err) { app = angular.module("templates", []); }
app.run(["$templateCache", function($templateCache) {
"use strict";

$templateCache.put("app/components/root.tpl.html","<div class=\" container-fluid\">\n" +
    "    <!--<div class=\"row\">{{$ctrl.globalState}}</div>-->\n" +
    "\n" +
    "    <td-navigation ng-if=\"!$ctrl.globalState.isFormCompleted\"></td-navigation>\n" +
    "</div>\n" +
    "<div class=\" container-fluid\">\n" +
    "    <div class=\"row\">\n" +
    "        <td-wizard class=\"col td-wizard\" ng-if=\"!$ctrl.globalState.isFormCompleted\"></td-wizard>\n" +
    "        <td-sidebar user-data=\"$ctrl.userData\" class=\"col-3 td-car-details hidden-md-down\" ng-if=\"!$ctrl.globalState.isFormCompleted\" user-data=\"$ctrl.userData\"></td-sidebar>\n" +
    "    </div>\n" +
    "\n" +
    "    <td-complete-step ng-if=\"$ctrl.globalState.isFormCompleted\"></td-complete-step>\n" +
    "</div>")

$templateCache.put("app/components/complete-step/complete-step.tpl.html","<div class=\"td-complete-step\">\n" +
    "    <div class=\"td-complete-step__content\">\n" +
    "        <span class=\"td-complete-step__title\">Booking completed</span>\n" +
    "        <span class=\"td-complete-step__desc\">You can now manage your booking from the test drive portal</span>\n" +
    "        <img class=\"td-complete-step__img\" src=\"/img/finish-step.png\">\n" +
    "        <a href=\"http://portal.dev.test-drive.tech\" target=\"_blank\" class=\"td-next-step-btn td-complete-step__btn\">Go to test drive portal</a>\n" +
    "    </div>\n" +
    "</div>")

$templateCache.put("app/components/navigation/navigation.tpl.html","<div class=\"row td-tabs\">\n" +
    "\n" +
    "    <td-tab\n" +
    "            ng-repeat=\"tab in $ctrl.widgetTabs\"\n" +
    "            class=\"col-12 col-md td-nav__tab\"\n" +
    "\n" +
    "            title=\"tab.title\"\n" +
    "            tab-data=\"$ctrl.widgetTabs[tab.id]\"\n" +
    "            tab-id=\"tab.id\"\n" +
    "            switch-tab=\"$ctrl.switchTab(tab.id)\">\n" +
    "    </td-tab>\n" +
    "</div>")

$templateCache.put("app/components/sidebar/sidebar.tpl.html","<div class=\"row\">\n" +
    "    <div class=\"col-12\">\n" +
    "        <img class=\"img-fluid\" src=\"{{$ctrl.userData.car.img}}\">\n" +
    "    </div>\n" +
    "    <div class=\"col-12\">\n" +
    "        <span class=\"td-car-details__title\">{{$ctrl.userData.car.title}}</span>\n" +
    "    </div>\n" +
    "    <div class=\"row td-car-details__content\">\n" +
    "        <div class=\"col-12\">\n" +
    "            <div class=\"top-buffer-14\">\n" +
    "                <div>\n" +
    "                    <div class=\"td-car-details__option-title\">\n" +
    "                        Engine\n" +
    "                    </div>\n" +
    "                    <div class=\"td-car-details__option-text\">\n" +
    "                        {{$ctrl.userData.car.engine}}\n" +
    "                        <!--3.8 litre twin-turbo V6-->\n" +
    "                    </div>\n" +
    "                </div>\n" +
    "            </div>\n" +
    "        </div>\n" +
    "\n" +
    "        <div class=\"top-buffer-14\">\n" +
    "            <div class=\"col-6\">\n" +
    "                <div class=\"td-car-details__option-title\">\n" +
    "                    Year\n" +
    "                </div>\n" +
    "                <div class=\"td-car-details__option-text\">\n" +
    "                    {{$ctrl.userData.car.year}}\n" +
    "                    <!--2016-->\n" +
    "                </div>\n" +
    "            </div>\n" +
    "            <div class=\"col-6\">\n" +
    "                <div class=\"td-car-details__option-title\">\n" +
    "                    Colour\n" +
    "                </div>\n" +
    "                <div class=\"td-car-details__option-text\">\n" +
    "                    {{$ctrl.userData.car.colour}}\n" +
    "                </div>\n" +
    "            </div>\n" +
    "        </div>\n" +
    "        <div class=\"top-buffer-14\">\n" +
    "            <div class=\"col-6\" ng-if=\"!!$ctrl.userData.car.transmission\">\n" +
    "                <div class=\"td-car-details__option-title\">\n" +
    "                    Transmission\n" +
    "                </div>\n" +
    "                <div class=\"td-car-details__option-text\">\n" +
    "                    {{$ctrl.userData.car.transmission}}\n" +
    "                </div>\n" +
    "            </div>\n" +
    "            <div class=\"col-6\" ng-if=\"$ctrl.userData.car.fuel\">\n" +
    "                <div class=\"td-car-details__option-title\">\n" +
    "                    Fuel\n" +
    "                </div>\n" +
    "                <div class=\"td-car-details__option-text\">\n" +
    "                    {{$ctrl.userData.car.fuel}}\n" +
    "                </div>\n" +
    "            </div>\n" +
    "        </div>\n" +
    "    </div>\n" +
    "    <div class=\"col-12 td-drive-details\">\n" +
    "        <div class=\"row td-drive-details__content\">\n" +
    "            <div class=\"col-12 td-car-details__options-row\"\n" +
    "                 ng-if=\"$ctrl.userData.calendar.date ||  $ctrl.userData.calendar.time\">\n" +
    "                <img class=\"td-drive-details__row-image\" src=\"/img/icon-date.jpg\">\n" +
    "                <span class=\"td-drive-details__row-text\">{{$ctrl.userData.calendar.date}} - {{$ctrl.userData.calendar.time}}</span>\n" +
    "            </div>\n" +
    "            <div class=\"col-12 td-car-details__options-row\" ng-if=\"$ctrl.userData.expert.title\">\n" +
    "                <img class=\"td-drive-details__row-image\" src=\"/img/icon-expert.jpg\">\n" +
    "                <span class=\"td-drive-details__row-text\">{{$ctrl.userData.expert.title}}</span>\n" +
    "            </div>\n" +
    "            <div class=\"col-12 td-car-details__options-row\" ng-if=\"$ctrl.userData.beverage.title\">\n" +
    "                <img class=\"td-drive-details__row-image\" src=\"/img/icon-beverage.jpg\">\n" +
    "                <span class=\"td-drive-details__row-text\">{{$ctrl.userData.beverage.title}}</span>\n" +
    "            </div>\n" +
    "            <div class=\"col-12 td-car-details__options-row-last\" ng-if=\"$ctrl.userData.road.title\">\n" +
    "                <img class=\"td-drive-details__row-image\" src=\"/img/icon-road.jpg\">\n" +
    "                <span class=\"td-drive-details__row-text\">{{$ctrl.userData.road.title}}</span>\n" +
    "            </div>\n" +
    "        </div>\n" +
    "    </div>\n" +
    "</div>")

$templateCache.put("app/components/wizard/wizard.tpl.html","<td-date\n" +
    "        ng-if=\"$ctrl.widgetTabs['time'].isActive\"\n" +
    "        tab-id=\"'time'\"\n" +
    "        complete-step=\"$ctrl.completeStep(tabId)\"\n" +
    "        user-data=\"$ctrl.userData\">\n" +
    "</td-date>\n" +
    "<td-expert\n" +
    "        ng-if=\"$ctrl.widgetTabs['expert'].isActive\"\n" +
    "        complete-step=\"$ctrl.completeStep(tabId)\"\n" +
    "        tab-id=\"'expert'\"\n" +
    "        user-data=\"$ctrl.userData\">\n" +
    "</td-expert>\n" +
    "<td-beverage\n" +
    "        ng-if=\"$ctrl.widgetTabs['beverage'].isActive\"\n" +
    "        complete-step=\"$ctrl.completeStep(tabId)\"\n" +
    "        tab-id=\"'beverage'\"\n" +
    "        user-data=\"$ctrl.userData\">\n" +
    "</td-beverage>\n" +
    "<td-road\n" +
    "        ng-if=\"$ctrl.widgetTabs['road'].isActive\"\n" +
    "        complete-step=\"$ctrl.completeStep(tabId)\"\n" +
    "        tab-id=\"'road'\"\n" +
    "        user-data=\"$ctrl.userData\">\n" +
    "</td-road>\n" +
    "<td-user-details\n" +
    "        ng-if=\"$ctrl.widgetTabs['details'].isActive\"\n" +
    "        complete-step=\"$ctrl.completeStep(tabId)\"\n" +
    "        complete-form=\"$ctrl.completeForm()\"\n" +
    "        tab-id=\"'details'\"\n" +
    "        user-data=\"$ctrl.userData\">\n" +
    "</td-user-details>\n" +
    "\n" +
    "\n" +
    "\n" +
    "")

$templateCache.put("app/components/shared/card/card.tpl.html","<div class=\"td-card\" ng-class=\"$ctrl.isActiveCard ? 'td-card--active' : ''\">\n" +
    "    <div class=\"row align-items-center\">\n" +
    "        <div class=\"td-card__image-column col-12 col-sm-3\">\n" +
    "            <div class=\"td-card__image-container\" ng-class=\"{'td-card__image-container--no-image' : !$ctrl.cardImage}\">\n" +
    "\n" +
    "\n" +
    "                <img ng-if=\"$ctrl.cardImage\" class=\"td-card__image\" src=\"{{$ctrl.cardImage}}\">\n" +
    "            </div>\n" +
    "        </div>\n" +
    "        <div class=\"td-card__content col-12 col-sm-9\">\n" +
    "            <div class=\"td-card__title\">{{$ctrl.cardTitle}}</div>\n" +
    "            <div class=\"td-card__desc\">{{$ctrl.cardDesc}}</div>\n" +
    "        </div>\n" +
    "    </div>\n" +
    "</div>")

$templateCache.put("app/components/shared/tabs/tab.tpl.html","<div class=\"td-tab-header\"\n" +
    "     ng-class=\"{'td-tab-header-completed': $ctrl.tabData.isCompleted, 'td-tab-header-active' : $ctrl.tabData.isActive, 'td-tab-header-locked' : $ctrl.tabData.isLocked}\"\n" +
    "     ng-click=\"$ctrl.switchTabInner()\">\n" +
    "     <span class=\"td-tab-header__title\">{{$ctrl.title}}</span>\n" +
    "</div>\n" +
    "<!--{{$ctrl.tabData.isActive}}<br>-->\n" +
    "<!--{{$ctrl.tabData.isLocked}}<br>-->\n" +
    "<!--{{$ctrl.tabData.isCompleted}}<br>-->")

$templateCache.put("app/components/steps/beverage/beverage.tpl.html","<div class=\"row user-details\">\n" +
    "<div class=\"col\">\n" +
    "    <div class=\"user-details__heading\">Select Beverage</div>\n" +
    "    <div class=\"user-details__subheading\">While you taking a Test Drive you can get free drinks</div>\n" +
    "</div>\n" +
    "</div>\n" +
    "<div class=\"row td-card-container\">\n" +
    "    <td-card class=\"col-md-6 col-sm-12 td-card-root\"\n" +
    "             ng-repeat=\"beverage in $ctrl.beverages\"\n" +
    "             card-image=\"beverage.photo_url\"\n" +
    "             card-title=\"beverage.title\"\n" +
    "             card-desc=\"beverage.description\"\n" +
    "             is-active-card=\"beverage.title == $ctrl.userData.beverage.title\"\n" +
    "             ng-click=\"$ctrl.beverageChanged(beverage.title)\">\n" +
    "    </td-card>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"row\">\n" +
    "    <div class=\"col td-next-step-btn\" ng-click=\"$ctrl.completeStepInner()\">Next step</div>\n" +
    "</div>")

$templateCache.put("app/components/steps/date/date.tpl.html","<div class=\"row\">\n" +
    "    <div class=\"col-md-6 col-sm-12\">\n" +
    "        <div class=\"tab-title\">Select Date</div>\n" +
    "        <md-calendar ng-if=\"$ctrl.openHours\" class=\"fixed-calendar td-calendar\"\n" +
    "                     md-min-date=\"$ctrl.minDate\"\n" +
    "                     md-max-date=\"$ctrl.maxDate\"\n" +
    "                     md-date-filter=\"$ctrl.dateFilter\"\n" +
    "                     ng-model=\"$ctrl.myDate\" ng-change=\"$ctrl.dateChanged()\"></md-calendar>\n" +
    "    </div>\n" +
    "    <div class=\"col-md-6 col-sm-12\">\n" +
    "        <div class=\"tab-title td-time-select__title\">Select Time</div>\n" +
    "        <div class=\"td-time-select__container\">\n" +
    "            <div class=\"row\">\n" +
    "                <div class=\"col td-time-select__item\"\n" +
    "                     ng-repeat=\"time in $ctrl.timeIntervals\"\n" +
    "                     ng-click=\"$ctrl.timeChanged(time)\"\n" +
    "                     ng-class=\"{'td-time-select__item--active' : $ctrl.userData.calendar.time == time}\">{{time}}\n" +
    "                </div>\n" +
    "            </div>\n" +
    "        </div>\n" +
    "    </div>\n" +
    "</div>\n" +
    "<div class=\"row\">\n" +
    "    <div class=\"col-12\">\n" +
    "        <div class=\"td-next-step-btn\" ng-click=\"$ctrl.completeStepInner()\">Next step</div>\n" +
    "    </div>\n" +
    "</div>")

$templateCache.put("app/components/steps/expert/expert.tpl.html","<div class=\"row user-details\">\n" +
    "    <div class=\"col\">\n" +
    "        <div class=\"user-details__heading\">Select an expert</div>\n" +
    "        <div class=\"user-details__subheading\">He or she will be there to assist you on the day of your test drive.</div>\n" +
    "    </div>\n" +
    "</div>\n" +
    "<div class=\"row td-card-container\">\n" +
    "    <td-card class=\"col-md-6 col-sm-12 td-card-root\"\n" +
    "             ng-repeat=\"expert in $ctrl.experts\"\n" +
    "             card-image=\"expert.photo_url\"\n" +
    "             card-title=\"expert.title\"\n" +
    "             card-desc=\"expert.description\"\n" +
    "             is-active-card=\"expert.title == $ctrl.userData.expert.title\"\n" +
    "             ng-click=\"$ctrl.expertChanged(expert.title)\">\n" +
    "    </td-card>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"row\">\n" +
    "    <div class=\"col-12\">\n" +
    "        <div class=\"td-next-step-btn\" ng-click=\"$ctrl.completeStepInner()\">Next step</div>\n" +
    "    </div>\n" +
    "</div>")

$templateCache.put("app/components/steps/registration/user-details.tpl.html","<div class=\"user-details\">\n" +
    "    <div class=\"user-details__heading\">New customer</div>\n" +
    "    <div class=\"user-details__subheading\">Create an account to track and edit the details of your test drive booking</div>\n" +
    "</div>\n" +
    "\n" +
    "<br>\n" +
    "\n" +
    "<div >\n" +
    "    <md-input-container class=\"md-block\">\n" +
    "        <label>First Name</label>\n" +
    "        <input md-maxlength=\"30\" required name=\"name\" ng-model=\"$ctrl.userData.user.firstName\"/>\n" +
    "    </md-input-container>\n" +
    "\n" +
    "    <md-input-container class=\"md-block\">\n" +
    "        <label>Last Name</label>\n" +
    "        <input name=\"social\" required ng-model=\"$ctrl.userData.user.lastName\"/>\n" +
    "    </md-input-container>\n" +
    "</div>\n" +
    "<div>\n" +
    "    <md-input-container class=\"md-block\" >\n" +
    "        <label>Email</label>\n" +
    "        <input name=\"email\" ng-model=\"$ctrl.userData.user.email\"\n" +
    "               required minlength=\"4\" maxlength=\"100\" ng-pattern=\"/^.+@.+\\..+$/\" />\n" +
    "    </md-input-container>\n" +
    "\n" +
    "    <md-input-container class=\"md-block\">\n" +
    "        <label>Phone Number: (000) 000-0000</label>\n" +
    "        <input name=\"phone\" ng-model=\"$ctrl.userData.user.phone\" ng-pattern=\"/^[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}$/\" />\n" +
    "    </md-input-container>\n" +
    "</div>\n" +
    "\n" +
    "\n" +
    "\n" +
    "<div class=\"user-details__button-container\">\n" +
    "    <div class=\"user-details__next-button\" ng-click=\"$ctrl.createAccountAndMakeBooking()\">Create account & make my booking</div>\n" +
    "\n" +
    "</div>")

$templateCache.put("app/components/steps/road/road.tpl.html","<div class=\"row user-details\">\n" +
    "    <div class=\"col\">\n" +
    "    <div class=\"user-details__heading\">Select preferred route</div>\n" +
    "    <div class=\"user-details__subheading\">Select the type of drive you'd like to take on your test drive</div>\n" +
    "    </div>\n" +
    "</div>\n" +
    "<div class=\"row td-card-container\">\n" +
    "    <td-card class=\"col-md-6 col-sm-12 td-card-root\"\n" +
    "             ng-repeat=\"road in $ctrl.roads\"\n" +
    "             card-image=\"road.photo_url\"\n" +
    "             card-title=\"road.title\"\n" +
    "             card-desc=\"road.description\"\n" +
    "             is-active-card=\"road.title == $ctrl.userData.road.title\"\n" +
    "             ng-click=\"$ctrl.expertChanged(road.title)\">\n" +
    "    </td-card>\n" +
    "</div>\n" +
    "<div class=\"row\">\n" +
    "    <div class=\"td-next-step-btn\" ng-click=\"$ctrl.completeStepInner()\">Next step</div>\n" +
    "</div>\n" +
    "")
}]);
})();
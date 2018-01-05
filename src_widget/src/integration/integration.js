(function () {

//=======================================================================//
// Widget buttons injector                                               //
//=======================================================================//

    var MarinButtonsInjector = {

        /// variables
        siteId: null,
        vehicle: {
            title: null,
            imageUrl: null,
            vdpUrl: null,
            vin: null,
            year: null
        },

        /// constructor
        init: function (siteId) {
            var self = this;
            self.siteId = siteId;

            if (self.isCurrentPageVdp()) {
                self.appendTestdriveVdpButton();
                self.appendTestdriveVdpButtonStyles();
            }
        },

        /// methods
        isCurrentPageVdp: function () {
            var element = document.getElementById('details-page-ctabox');

            if (element) {
                return true;
            }
            return false;
        },

        appendTestdriveVdpButton: function () {
            var self = this;

            var title = document.getElementsByClassName("vehicle-title")[0];
            if (title) {
                self.vehicle.title = title.innerHTML;
            }

            var image = document.querySelector(".owl-carousel .owl-item img");
            if (image) {
                self.vehicle.imageUrl = image.getAttribute('src');
            }

            self.vehicle.vdpUrl = window.location.href;

            var urlParams = self.vehicleDetailsFromUrl(window.location.pathname);

            self.vehicle.vin = urlParams.vin;
            self.vehicle.year = urlParams.year;

            var detailsPageCtabox = document.getElementById('details-page-ctabox');

            var btn = document.createElement("BUTTON");
            btn.classList.add("marin-vdp-button");

            var carDetailsFromHtml = self.vehicleDetailsFromHtml();

            btn.onclick = function () {
                window.TestDrive.openTestDrive({
                    carVin: self.vehicle.vin,
                    carImageUrl: self.vehicle.imageUrl,
                    vdpUrl: self.vehicle.vdpUrl,
                    carTitle: self.vehicle.title,
                    carEngine: carDetailsFromHtml.engine,
                    carYear: self.vehicle.year,
                    carColor: carDetailsFromHtml.exterior,
                    carTransmission: carDetailsFromHtml.trans,
                    carFuel: null
                });
            };
            detailsPageCtabox.appendChild(btn);
        },

        appendTestdriveVdpButtonStyles: function () {
            var css = '.marin-vdp-button { width: 100%; ' +
                'display: table;' +
                'max-width: 650px;' +
                'margin: 0 auto;' +
                'border: 0px;' +
                'height: 54px;' +
                'background-color: #176db7;' +
                'background-image: url(https://widget.testdrive.pw/integration/img/marin-vdp-button.png);' +
                'background-repeat: no-repeat;' +
                'background-position: center;' +
                '}' +
                '' +
                '.marin-vdp-button:hover {' +
                'background-color: #11528a;' +
                '}';
            var head = document.head || document.getElementsByTagName('head')[0];
            var style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);
        },

        /// helpers
        vehicleDetailsFromUrl: function (vdpPageUrl) {
            var result = {
                year: null,
                vin: null
            };

            var vehicleUrl = vdpPageUrl.split('/')[2];
            var vehicleUrlParams;
            if (vehicleUrl) {
                vehicleUrlParams = vehicleUrl.split('-');
            }

            if (vehicleUrlParams[1]) {
                result.year = vehicleUrlParams[1];
            }
            result.vin = vehicleUrlParams[vehicleUrlParams.length - 1];

            return result;
        },

        vehicleDetailsFromHtml: function() {
            var result = {
                body: null,
                exterior: null,
                engine: null,
                trans: null
            };

            var infoWrapper = document.querySelector(".details-page-row .basic-info-wrapper");
            var infoWrapperText = null;

            if (infoWrapper) {
                infoWrapperText = infoWrapper.innerText;
            } else {
                return result;
            }

            var bodyRegex = /Body: (.*)/g;
            var bodyMatch = bodyRegex.exec(infoWrapperText);
            result.body = bodyMatch ? bodyMatch[1] : null;

            var exteriorRegex = /Exterior: (.*)/g;
            var exteriorMatch = exteriorRegex.exec(infoWrapperText);
            result.exterior = exteriorMatch ? exteriorMatch[1] : null;

            var engineRegex = /Engine: (.*)/g;
            var engineMatch = engineRegex.exec(infoWrapperText);
            result.engine = engineMatch ? engineMatch[1] : null;

            var transRegex = /Trans: (.*)/g;
            var transMatch = transRegex.exec(infoWrapperText);
            result.trans = transMatch ? transMatch[1] : null;

            return result;
        }

    };

//=======================================================================//
// TestDrive Initialization                                              //
//=======================================================================//

    window.TestDrive = window.TestDrive || (function () {
        var _SiteId = '%siteId%';
        var _WidgetUrl = '%widgetUrl%'; // https://widget.testdrive.pw/ // %widgetUrl%
        var _IntegrateAutomatically = null;

        var _appendTestDriveFrame = function (vin, vdpUrl, img, title, engine, year, colour, transmission, fuel) {
            var hash = Math.random().toString(36).substring(7);
            var url = `${_WidgetUrl}?site_id=${_SiteId}&vin=${vin}&vdpUrl=${vdpUrl}&imageUrl=${img}&title=${title}&engine=${engine}&year=${year}&colour=${colour}&transmission=${transmission}&fuel=${fuel}&hash=${hash}`;

            var html =
                `<div class="test-drive__content">
                 <div class="frame-header">
                    <div class="frame-header__title">Test Drive Booking</div>
                    <div class="frame-header__cross" onclick="TestDrive.closeTestDrive()">&#10006;</div>
                 </div>
                 <img id="test-drive__frame-spinner" src="http://www.testdrive.pw/spinner.gif">
                 <iframe class="test-drive__frame" src="${encodeURI(url)}" frameBorder="0" onload="document.getElementById('test-drive__frame-spinner').style.display='none';"></iframe>
             </div>`;

            document.getElementsByClassName('test-drive')[0].innerHTML = html;
        };

        var _appendTestDriveFrameWrapper = function () {
            var elemDiv = document.createElement('div');
            elemDiv.className = "test-drive";

            if (!document.body) {
                document.body = document.createElement("body");
                document.body.style.display = 'table';
            }

            document.body.appendChild(elemDiv);
        };

        var _showTestDriveFrame = function () {
            document.getElementsByClassName('test-drive')[0].classList.add('test-drive-visible');
            document.documentElement.classList.add('vipdrv--disable-scroll');
            window.scrollTo(0, 0);

            setTimeout(function () {
                document.getElementsByClassName('test-drive__content')[0].classList.add('test-drive__content-visible');
            }, 200);
        };

        var _hideTestDriveFrame = function () {
            document.getElementsByClassName('test-drive__content')[0].classList.remove('test-drive__content-visible');
            document.documentElement.classList.remove('vipdrv--disable-scroll');

            setTimeout(function () {
                document.getElementsByClassName('test-drive')[0].classList.remove('test-drive-visible');
            }, 200);
        };

        var _changeMobileBrowserBarColor = function () {
            var head = document.head;

            var chromeMeta = document.createElement('meta');
            chromeMeta.id = 'TestDriveChromeMeta';
            chromeMeta.name = 'theme-color';
            chromeMeta.content = "#007bff";
            head.appendChild(chromeMeta);

            var windowsPhoneMeta = document.createElement('meta');
            windowsPhoneMeta.id = 'TestDriveWindowsPhoneMeta';
            windowsPhoneMeta.name = 'msapplication-navbutton-color';
            windowsPhoneMeta.content = "#007bff";
            head.appendChild(windowsPhoneMeta);

            var safariMeta = document.createElement('meta');
            safariMeta.id = 'TestDriveSafariMeta';
            safariMeta.name = 'apple-mobile-web-app-status-bar-style';
            safariMeta.content = "#007bff";

            head.appendChild(safariMeta);
        };

        var _restoreDefaultMobileBrowserBarColor = function () {
            var colorHeaders = ['TestDriveChromeMeta', 'TestDriveWindowsPhoneMeta', 'TestDriveSafariMeta'];

            for (var key in colorHeaders) {
                var id = colorHeaders[key];
                var element = document.getElementById(id);
                if (element) {
                    element.content = "#f2f2f2";
                    element.remove();
                }
            }
        };

        var _appendTestDriveFrameWrapperStyles = function () {
            var head = document.head;

            var link = document.createElement('link');
            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = _WidgetUrl + '/Integration/integration.css';
            head.appendChild(link);
        };

        var _addEventListeners = function () {
            document.onkeydown = function (evt) {
                evt = evt || window.event;
                var isEscape = false;
                if ("key" in evt) {
                    isEscape = (evt.key == "Escape" || evt.key == "Esc");
                } else {
                    isEscape = (evt.keyCode == 27);
                }
                if (isEscape) {
                    _hideTestDriveFrame();
                }
            };

            window.onclick = function (event) {
                var modal = document.getElementsByClassName('test-drive')[0];

                if (event.target == modal) {
                    _hideTestDriveFrame();
                }
            };

            window.addEventListener("resize", function () {
                var div = document.getElementsByClassName('test-drive__frame')[0];
                if (div) {
                    div.style.height = 'calc(100% - 50px)';
                }
            });
        };

        var _integrateWidgetButtons = function (siteId, integrateAutomatically) {
            if (integrateAutomatically) {
                MarinButtonsInjector.init(siteId);
            }
        };

        var _initExtensions = function() {
            Element.prototype.remove = function () {
                this.parentElement.removeChild(this);
            };
            NodeList.prototype.remove = HTMLCollection.prototype.remove = function () {
                for (var i = this.length - 1; i >= 0; i--) {
                    if (this[i] && this[i].parentElement) {
                        this[i].parentElement.removeChild(this[i]);
                    }
                }
            };
        };

        return {
            init: function (Args) {
                _SiteId = Args.SiteId || _SiteId;
                _IntegrateAutomatically = Args.IntegrateAutomatically || true;

                _appendTestDriveFrameWrapper();
                _appendTestDriveFrameWrapperStyles();
                _addEventListeners();
                _initExtensions();
                _integrateWidgetButtons(_SiteId, _IntegrateAutomatically);

            },
            openTestDrive: function (Args) {
                var carVin = Args.carVin || "";
                var vdpUrl = Args.vdpUrl || "";
                var carImageUrl = Args.carImageUrl || "";
                var carTitle = Args.carTitle || "";
                var carEngine = Args.carEngine || "";
                var carYear = Args.carYear || "";
                var carColor = Args.carColor || "";
                var carTransmission = Args.carTransmission || "";
                var carFuel = Args.carFuel || "";

                _appendTestDriveFrame(carVin, vdpUrl, carImageUrl, carTitle, carEngine, carYear, carColor, carTransmission, carFuel);
                _changeMobileBrowserBarColor();
                _showTestDriveFrame();
            },
            closeTestDrive: function () {
                _restoreDefaultMobileBrowserBarColor();
                _hideTestDriveFrame();
            },
            getSiteId: function () {
                return _SiteId;
            }
        };
    }());

})();

//=======================================================================//
// Debug                                                                 //
//=======================================================================//
//
// https://widget.testdrive.pw
//

// window.TestDrive.init({SiteId: '28'});


(function () {

//=======================================================================//
// Widget buttons injector                                               //
//=======================================================================//

    var TruckworldButtonsInjector = {
        /// initialization
        init: function (injectWidgetToVlp, injectWidgetToVdp) {
            var self = this;

            if (injectWidgetToVdp) {
                // self.appendTestdriveVdpButton();
                self.appendTestDriveVdpButtonStyles();
            }

            if (injectWidgetToVlp) {
                // self.initTestDriveVlpButtons();
                self.appendTestDriveVlpButtonStyles();
            }
        },

        appendTestDriveVdpButtonStyles: function () {
            var css = '.btn-testdrive-large { display: table !important; }';
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

        appendTestDriveVlpButtonStyles: function () {
            var css = '.btn-testdrive { display: table !important; }';
            var head = document.head || document.getElementsByTagName('head')[0];
            var style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);
        }
    };

    var MarinButtonsInjector = {

        /// initialization
        init: function (injectWidgetToVlp, injectWidgetToVdp) {
            var self = this;

            if (self.isCurrentPageVdp() && injectWidgetToVdp) {
                self.appendTestdriveVdpButton();
                self.appendTestDriveVdpButtonStyles();
            }

            if (self.isCurrentPageVlp() && injectWidgetToVlp) {
                self.initTestDriveVlpButtons();
                self.appendTestDriveVlpButtonStyles();
                self.initVlpListener();
            }
        },

        /// vlp injector
        initVlpListener: function () {
            var self = this;

            var resultsTable = document.getElementsByClassName("results_table")[0];
            self.observeDOM(resultsTable, function () {
                console.log('dom changed');
                self.initTestDriveVlpButtons();
            });
        },

        isCurrentPageVlp: function () {
            var resultsPage = document.getElementById("results-page");
            var resultsTable = document.getElementsByClassName("results_table")[0];
            if (!resultsTable || !resultsPage) {
                return false;
            }
            return true;
        },

        initTestDriveVlpButtons: function () {
            var self = this;

            var resultsTable = document.getElementsByClassName("results_table")[0];
            var resultsTableNodes = resultsTable.childNodes;
            var tableBodyNode = null;
            for (var i = 0; i < resultsTableNodes.length; i++) {
                var node = resultsTableNodes[i];
                if (node.nodeName.toLowerCase() == "tbody") {
                    tableBodyNode = node;
                }
            }

            if (!tableBodyNode) {
                console.log('Could not find tableBodyNode');
                return false;
            }

            var tableBodyChildNodes = tableBodyNode.childNodes;
            for (var i = 0; i < tableBodyChildNodes.length; i++) {
                var node = tableBodyChildNodes[i];
                if (node.nodeName.toLowerCase() == "tr" &&
                    node.classList.contains("hidden-xs") &&
                    !node.classList.contains("vipdrv-added")) {

                    node.classList.add('vipdrv-added');
                    console.log('button added');
                    var vehicle = self.parseVehicleDetailsFromNode(node);
                    self.addTestDriveButtonToVehicleNode(node, vehicle);
                }
            }
        },

        appendTestDriveVlpButtonStyles: function () {
            var css = '.vipdrv-marin-vlp-button { ' +
                'width: 100% !important; ' +
                'display: table;' +
                '}' +
                '' +
                '.vipdrv-marin-vdp-button:hover {' +

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

        addTestDriveButtonToVehicleNode: function (node, vehicleObject) {
            var btn = document.createElement("BUTTON");
            btn.classList.add("vipdrv-marin-vlp-button", "button-bar-item", "primary-button", "button");
            var textNode = document.createTextNode("VIPdrv - Test Drive");
            btn.appendChild(textNode);
            btn.onclick = function () {
                window.TestDrive.openTestDrive(vehicleObject);
            };

            var priceBlock = node.querySelector('.vehicle-content .vehicle-price');
            if (priceBlock) {
                priceBlock.appendChild(btn);
            }
        },

        parseVehicleDetailsFromNode: function (node) {
            var vehicle = {
                carVin: null,
                carImageUrl: null,
                vdpUrl: null,
                carTitle: null,
                carEngine: null,
                carYear: null,
                carColor: null,
                carTransmission: null,
                carFuel: null
            };

            var vehicleImageNode = node.querySelector('.vehicle-image img');
            if (vehicleImageNode) {
                var imageUrl = null;
                var counter = 0;

                var interval = setInterval(function () {
                    imageUrl = vehicleImageNode.getAttribute('src');
                    var imageFileName = imageUrl.split('/').slice(-1)[0];

                    if (imageFileName == 'loading.gif') {

                        console.log(node);
                        console.log(imageFileName);
                    } else {
                        clearInterval(interval);
                    }

                    counter++;
                    if (counter > 15) { // wait 3 seconds for vehicle image loaded
                        clearInterval(interval);
                    }
                }, 200);
                vehicle.carImageUrl = imageUrl;
            }

            var vehicleTitleNode = node.querySelector('.vehicle-title h2 a');
            if (vehicleTitleNode) {
                vehicle.vdpUrl = vehicleTitleNode.getAttribute('href');
                vehicle.carTitle = vehicleTitleNode.textContent;
            }

            var vinNode = node.querySelector('.vinstock');
            if (vinNode) {
                var vinRegex = /VIN: (.*)/g;
                var vinMatch = vinRegex.exec(vinNode.textContent);
                vehicle.carVin = vinMatch ? vinMatch[1] : null;
            }

            // TODO: add year

            var vehicleContent = node.querySelector(".vehicle-content");
            if (vehicleContent) {
                var textContent = vehicleContent.innerText;

                var bodyRegex = /Body: (.*)/g;
                var bodyMatch = bodyRegex.exec(textContent);
                // vehicle.body = bodyMatch ? bodyMatch[1] : null; // TODO: add body parameter

                var exteriorRegex = /Exterior: (.*)/g;
                var exteriorMatch = exteriorRegex.exec(textContent);
                vehicle.carColor = exteriorMatch ? exteriorMatch[1] : null;

                var engineRegex = /Engine: (.*)/g;
                var engineMatch = engineRegex.exec(textContent);
                vehicle.carEngine = engineMatch ? engineMatch[1] : null;

                var transRegex = /Trans: (.*)/g;
                var transMatch = transRegex.exec(textContent);
                vehicle.carTransmission = transMatch ? transMatch[1] : null;
            }

            return vehicle;
        },

        /// vdp injector
        isCurrentPageVdp: function () {
            var element = document.getElementById('details-page-ctabox');

            if (element) {
                return true;
            }
            return false;
        },

        appendTestdriveVdpButton: function () {
            var self = this;

            var vdpVehicle = {
                title: null,
                imageUrl: null,
                vdpUrl: null,
                vin: null,
                year: null
            };

            var title = document.getElementsByClassName("vehicle-title")[0];
            if (title) {
                vdpVehicle.title = title.innerHTML;
            }

            var image = document.querySelector(".owl-carousel .owl-item img");
            if (image) {
                vdpVehicle.imageUrl = image.getAttribute('src');
            }

            vdpVehicle.vdpUrl = window.location.href;

            var urlParams = self.vehicleDetailsFromUrl(window.location.pathname);

            vdpVehicle.vin = urlParams.vin;
            vdpVehicle.year = urlParams.year;

            var detailsPageCtabox = document.getElementById('details-page-ctabox');

            var btn = document.createElement("BUTTON");
            btn.classList.add("vipdrv-marin-vdp-button");

            var infoWrapper = document.querySelector(".details-page-row .basic-info-wrapper");
            var infoWrapperText = null;

            if (infoWrapper) {
                infoWrapperText = infoWrapper.innerText;
            }
            var carDetailsFromHtml = self.vehicleDetailsFromVdpHtml(infoWrapperText);

            btn.onclick = function () {
                window.TestDrive.openTestDrive({
                    carVin: vdpVehicle.vin,
                    carImageUrl: vdpVehicle.imageUrl,
                    vdpUrl: vdpVehicle.vdpUrl,
                    carTitle: vdpVehicle.title,
                    carEngine: carDetailsFromHtml.engine,
                    carYear: vdpVehicle.year,
                    carColor: carDetailsFromHtml.exterior,
                    carTransmission: carDetailsFromHtml.trans,
                    carFuel: null
                });
            };
            detailsPageCtabox.appendChild(btn);
        },

        appendTestDriveVdpButtonStyles: function () {
            var css = '.vipdrv-marin-vdp-button { width: 100%; ' +
                'display: table;' +
                'max-width: 650px;' +
                'margin: 0 auto;' +
                'border: 0px;' +
                'height: 54px;' +
                'background-color: #176db7;' +
                'background-image: url(https://widget.testdrive.pw/integration/img/vipdrv-marin-vdp-button.png);' +
                'background-repeat: no-repeat;' +
                'background-position: center;' +
                '}' +
                '' +
                '.vipdrv-marin-vdp-button:hover {' +
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

        vehicleDetailsFromVdpHtml: function (plainText) {
            var result = {
                body: null,
                exterior: null,
                engine: null,
                trans: null
            };

            var bodyRegex = /Body: (.*)/g;
            var bodyMatch = bodyRegex.exec(plainText);
            result.body = bodyMatch ? bodyMatch[1] : null;

            var exteriorRegex = /Exterior: (.*)/g;
            var exteriorMatch = exteriorRegex.exec(plainText);
            result.exterior = exteriorMatch ? exteriorMatch[1] : null;

            var engineRegex = /Engine: (.*)/g;
            var engineMatch = engineRegex.exec(plainText);
            result.engine = engineMatch ? engineMatch[1] : null;

            var transRegex = /Trans: (.*)/g;
            var transMatch = transRegex.exec(plainText);
            result.trans = transMatch ? transMatch[1] : null;

            return result;
        },

        /// extensions
        observeDOM: (function () {
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver,
                eventListenerSupported = window.addEventListener;

            return function (obj, callback) {
                if (MutationObserver) {
                    // define a new observer
                    var obs = new MutationObserver(function (mutations, observer) {
                        if (mutations[0].addedNodes.length || mutations[0].removedNodes.length)
                            callback();
                    });
                    // have the observer observe foo for changes in children
                    obs.observe(obj, {childList: true, subtree: true});
                }
                else if (eventListenerSupported) {
                    obj.addEventListener('DOMNodeInserted', callback, false);
                    obj.addEventListener('DOMNodeRemoved', callback, false);
                }
            };
        })()
    };

//=======================================================================//
// TestDrive Initialization                                              //
//=======================================================================//

    window.TestDrive = window.TestDrive || (function () {
        var _SiteId = '%siteId%';
        var _WidgetUrl = '%widgetUrl%'; // https://widget.testdrive.pw/ // %widgetUrl%
        var _InjectWidgetToVlp = null;
        var _InjectWidgetToVdp = null;

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

        var _addTestDriveFrameEventListeners = function () {
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

        var _injectWidgetButtons = function (siteId, injectWidgetToVlp, injectWidgetToVdp) {
            if (!injectWidgetToVlp && !injectWidgetToVdp) {
                return false;
            }

            // TODO: hardcoded site id's
            if (siteId == 28) {
                TruckworldButtonsInjector.init(injectWidgetToVlp, injectWidgetToVdp);
            }

            if (siteId == 32) {
                MarinButtonsInjector.init(injectWidgetToVlp, injectWidgetToVdp);
            }
        };

        var _initExtensions = function () {
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
                _InjectWidgetToVlp = Args.injectWidgetToVlp || false;
                _InjectWidgetToVdp = Args.injectWidgetToVdp || false;

                _appendTestDriveFrameWrapper();
                _appendTestDriveFrameWrapperStyles();
                _addTestDriveFrameEventListeners();
                _initExtensions();
                _injectWidgetButtons(_SiteId, _InjectWidgetToVlp, _InjectWidgetToVdp);

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
//
// window.TestDrive.init({SiteId: '28'});
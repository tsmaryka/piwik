/*!
 * Piwik - free/libre analytics platform
 *
 * PageRenderer class for screenshot tests.
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

var VERBOSE = false;

// TODO: should refactor, move all event queueing logic to PageAutomation class and add .frame method to change context
var PageRenderer = function (baseUrl) {
    this.webpage = null;
    this.userAgent = null;

    this.queuedEvents = [];
    this.pageLogs = [];
    this.aborted = false;
    this.baseUrl = baseUrl;
    this.currentFrame = null;

    this.defaultWaitTime = 1000;
    this._isLoading = false;

    if (this.baseUrl.substring(-1) != '/') {
        this.baseUrl = this.baseUrl + '/';
    }
};

PageRenderer.prototype._recreateWebPage = function () {
    if (this.webpage) {
        this.webpage.close();
    }

    this.downloadedContents = null;

    this.webpage = require('webpage').create();
    this.webpage.viewportSize = {width:1350, height:768};
    if (this.userAgent) {
        this.webpage.settings.userAgent = this.userAgent;
    }

    this._setupWebpageEvents();
};

PageRenderer.prototype.setViewportSize = function (w, h) {
    this._viewportSizeOverride = {width: w, height: h};
};

PageRenderer.prototype.getCurrentUrl = function () {
    return this.webpage ? this.webpage.url : null;
};

// event queueing functions
PageRenderer.prototype.wait = function (waitTime) {
    this.queuedEvents.push([this._wait, waitTime]);
};

PageRenderer.prototype.sendMouseEvent = function (type, pos, waitTime) {
    this.queuedEvents.push([this._sendMouseEvent, waitTime, type, pos]);
};

PageRenderer.prototype.click = function () {
    var selector = arguments[0],
        waitTime = null,
        modifiers = [];

    for (var i = 1; i != arguments.length; ++i) {
        if (arguments[i] instanceof Array) {
            modifiers = arguments[i];
        } else {
            waitTime = arguments[i];
        }
    }

    this.queuedEvents.push([this._click, waitTime, selector, modifiers]);
};

PageRenderer.prototype.sendKeys = function (selector, keys, waitTime) {
    this.click(selector, 100);
    this.queuedEvents.push([this._keypress, waitTime, keys]);
};

PageRenderer.prototype.mouseMove = function (selector, waitTime) {
    this.queuedEvents.push([this._mousemove, waitTime, selector]);
};

PageRenderer.prototype.mousedown = function (selector, waitTime) {
    this.queuedEvents.push([this._mousedown, waitTime, selector]);
};

PageRenderer.prototype.mouseup = function (selector, waitTime) {
    this.queuedEvents.push([this._mouseup, waitTime, selector]);
};

PageRenderer.prototype.reload = function (waitTime) {
    this.queuedEvents.push([this._reload, waitTime]);
};

PageRenderer.prototype.load = function (url, waitTime) {
    this.queuedEvents.push([this._load, waitTime, url]);
};

PageRenderer.prototype.evaluate = function (impl, waitTime) {
    this.queuedEvents.push([this._evaluate, waitTime, impl]);
};

PageRenderer.prototype.downloadLink = function (selector, waitTime) {
    this.queuedEvents.push([this._downloadLink, waitTime, selector]);
};

PageRenderer.prototype.dragDrop = function (startSelector, endSelector, waitTime) {
    this.mousedown(startSelector, waitTime);
    this.mouseMove(endSelector, waitTime);
    this.mouseup(endSelector, waitTime);
};

// event impl functions
PageRenderer.prototype._wait = function (callback) {
    callback();
};

PageRenderer.prototype._sendMouseEvent = function (type, pos, callback) {
    this.webpage.sendEvent(type, pos.x, pos.y);
    callback();
};

PageRenderer.prototype._click = function (selector, modifiers, callback) {
    var position = this._getPosition(selector);

    if (modifiers.length) {
        var self = this;
        modifiers = modifiers.reduce(function (previous, mStr) {
            return self.webpage.event.modifier[mStr] | previous;
        }, 0);

        this.webpage.sendEvent('mousedown', position.x, position.y, 'left', modifiers);
        this.webpage.sendEvent('mouseup', position.x, position.y, 'left', modifiers);
    } else {
        this.webpage.sendEvent('click', position.x, position.y);
    }

    callback();
};

PageRenderer.prototype._keypress = function (keys, callback) {
    this.webpage.sendEvent('keypress', keys);

    callback();
};

PageRenderer.prototype._mousemove = function (selector, callback) {
    var position = this._getPosition(selector);
    this.webpage.sendEvent('mousemove', position.x, position.y);

    callback();
};

PageRenderer.prototype._mousedown = function (selector, callback) {
    var position = this._getPosition(selector);
    this.webpage.sendEvent('mousedown', position.x, position.y);

    callback();
};

PageRenderer.prototype._mouseup = function (selector, callback) {
    var position = this._getPosition(selector);
    this.webpage.sendEvent('mouseup', position.x, position.y);

    callback();
};

PageRenderer.prototype._reload = function (callback) {
    this.webpage.reload();

    callback();
};

PageRenderer.prototype._load = function (url, callback) {
    if (url.indexOf("://") === -1) {
        url = this.baseUrl + url;
    }

    this._recreateWebPage(); // calling open a second time never calls the callback
    this.webpage.open(url, function (status) {
        this.evaluate(function () {
            var $ = window.jQuery;
            if ($) {
                $('html').addClass('uiTest');
            }
        });

        if (callback) {
            callback(status);
        }
    });
};

PageRenderer.prototype._evaluate = function (impl, callback) {
    this.webpage.evaluate(function (js) {
        var $ = window.jQuery;
        eval("(" + js + ")();");
    }, impl.toString());

    callback();
};

PageRenderer.prototype._downloadLink = function (str, callback) {
    var response = this.webpage.evaluate(function (selector) {
        var $ = window.jQuery,
            url = $(selector).attr('href');

        return $.ajax({
            type: "GET",
            url: url,
            async: false
        }).responseText;
    }, str);

    this.downloadedContents = response;

    callback();
};

PageRenderer.prototype._getPosition = function (selector) {
    var pos = this.webpage.evaluate(function (selector) {
        var element = window.jQuery(selector),
            offset = element.offset();

        if (!offset
            || !element.length
        ) {
            // TODO: this should get captured and outputted as part of the web page logs failure info, but
            //       at the moment it doesn't
            console.log("ERROR: Cannot find element '" + selector + "'.");

            return null;
        }

        return {
            x: offset.left + element.width() / 2,
            y: offset.top + element.height() / 2
        };
    }, selector);

    return pos;
};

PageRenderer.prototype.contains = function (selector) {
    return this.webpage.evaluate(function (selector) {
        return $(selector).length != 0;
    }, selector);
};

// main capturing function
PageRenderer.prototype.capture = function (outputPath, callback, selector) {
    var self = this,
        timeout = setTimeout(function () {
            self.abort();
            callback(new Error("Screenshot load timeout."));
        }, 120 * 1000);

    if (this.webpage === null) {
        this._recreateWebPage();
    }

    var events = this.queuedEvents;
    this.queuedEvents = [];
    this.pageLogs = [];
    this.aborted = false;

    function setClipRect (page, selector) {
        if (!selector) {
            return;
        }

        var result = page.evaluate(function(selector) {
            var docWidth = $(document).width(),
                docHeight = $(document).height();

            function isInvalidBoundingRect (rect) {
                return !rect.width || !rect.height
                    || (rect.left < 0 && rect.right < 0)
                    || (rect.left > docWidth && rect.right > docWidth)
                    || (rect.top < 0 && rect.bottom < 0)
                    || (rect.top > docHeight && rect.bottom > docHeight);
            }

            var element = window.jQuery(selector);

            if (element && element.length) {
                var clipRect = {bottom: null, height: null, left: null, right: null, top: null, width: null};

                element.each(function (index, node) {
                    if (!$(node).is(':visible')) {
                        return;
                    }

                    var rect = $(node).offset();
                    rect.width = $(node).outerWidth();
                    rect.height = $(node).outerHeight();
                    rect.right = rect.left + rect.width;
                    rect.bottom = rect.top + rect.height;

                    if (isInvalidBoundingRect(rect)) {
                        // element is not visible
                        return;
                    }

                    if (null === clipRect.left || rect.left < clipRect.left) {
                        clipRect.left = rect.left;
                    }
                    if (null === clipRect.top || rect.top < clipRect.top) {
                        clipRect.top = rect.top;
                    }
                    if (null === clipRect.right || rect.right > clipRect.right) {
                        clipRect.right = rect.right;
                    }
                    if (null === clipRect.bottom || rect.bottom > clipRect.bottom) {
                        clipRect.bottom = rect.bottom;
                    }
                });

                clipRect.width  = clipRect.right - clipRect.left;
                clipRect.height = clipRect.bottom - clipRect.top;

                return clipRect;
            }

        }, selector);

        if (!result) {
            console.log("Cannot find element " + selector);
            return;
        }

        if (result && result.__isCallError) {
            throw new Error("Error while detecting element clipRect " + selector + ": " + result.message);
        }

        if (null === result.left
            || null === result.top
            || null === result.bottom
            || null === result.right
        ) {
            console.log("Element(s) " + selector + " found but none is visible");
            return;
        }

        page.clipRect = result;
    }

    this._executeEvents(events, function () {
        if (self.aborted) {
            return;
        }

        clearTimeout(timeout);

        try {
            var previousClipRect = self.webpage.clipRect;

            if (outputPath) {
                setClipRect(self.webpage, selector);

                self._setCorrectViewportSize();
                self.webpage.render(outputPath);
            }

            self._viewportSizeOverride = null;
            self.webpage.clipRect = previousClipRect;

            callback();
        } catch (e) {
            self._viewportSizeOverride = null;
            self.webpage.clipRect = previousClipRect;

            callback(e);
        }
    });
};

PageRenderer.prototype.abort = function () {
    this.aborted = true;
    this.webpage.stop();
};

PageRenderer.prototype._executeEvents = function (events, callback, i) {
    i = i || 0;

    var evt = events[i];
    if (!evt) {
        callback();
        return;
    }

    var impl = evt.shift(),
        waitTime = evt.shift() || this.defaultWaitTime;

    var self = this,
        waitForNextEvent = function () {
            self._waitForNextEvent(events, callback, i, waitTime);
        };

    evt.push(waitForNextEvent);

    try {
        impl.apply(this, evt);
    } catch (err) {
        self.pageLogs.push("Error: " + err.stack);
        waitForNextEvent();
    }
};

PageRenderer.prototype._getAjaxRequestCount = function () {
    return this.webpage.evaluate(function () {
        return window.globalAjaxQueue ? window.globalAjaxQueue.active : 0;
    });
};

PageRenderer.prototype._getImageLoadingCount = function () {
    return this.webpage.evaluate(function () {
        var count = 0;

        var cssImageProperties = ['backgroundImage', 'listStyleImage', 'borderImage', 'borderCornerImage', 'cursor'],
            matchUrl = /url\(\s*(['"]?)(.*?)\1\s*\)/g;

        if (!window._pendingImages) {
            window._pendingImages = {};
        }

        // check <img> elements and background URLs
        var elements = document.getElementsByTagName('*');
        for (var i = 0; i != elements.length; ++i) {
            var element = elements.item(i);
            if (element.tagName.toLowerCase() == 'img' // handle <img> elements
                && element.complete === false
            ) {
                count = count + 1;
            }

            if (typeof $ === "undefined") { // waiting for CSS depends on jQuery
                continue;
            }

            for (var j = 0; j != cssImageProperties.length; ++j) { // handle CSS image URLs
                var prop = $(element).css(cssImageProperties[j]);

                if (!prop) {
                    continue;
                }

                while (match = matchUrl.exec(prop)) {
                    var src = match[2];
                    if (window._pendingImages[src]) {
                        continue;
                    }

                    var img = new Image();

                    img.addEventListener('load', function () {
                        window._pendingImages[this.src] = true;
                    });

                    window._pendingImages[src] = img;
                    img.src = src;
                }
            }
        }

        for (var url in window._pendingImages) {
            if (typeof window._pendingImages[url] === 'object') {
                count = count + 1;
            }
        }

        return count;
    });
};

PageRenderer.prototype._waitForNextEvent = function (events, callback, i, waitTime) {
    var self = this;
    setTimeout(function () {
        if (self._getAjaxRequestCount() == 0
            && self._getImageLoadingCount() == 0
            && !self._isLoading
        ) {
            self._executeEvents(events, callback, i + 1);
        } else {
            self._waitForNextEvent(events, callback, i, waitTime);
        }
    }, waitTime);
};

PageRenderer.prototype._setCorrectViewportSize = function () {
    var viewportSize = this._viewportSizeOverride || {width:1350, height:768};

    this.webpage.viewportSize = viewportSize;
    var height = Math.max(viewportSize.height, this.webpage.evaluate(function() {
        return document.body.offsetHeight;
    }));
    this.webpage.viewportSize = {width: viewportSize.width, height: height};
};

PageRenderer.prototype._setupWebpageEvents = function () {
    var self = this;
    this.webpage.onError = function (message, trace) {
        var msgStack = ['Webpage error: ' + message];
        if (trace && trace.length) {
            msgStack.push('trace:');
            trace.forEach(function(t) {
                msgStack.push(' -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function + '")' : ''));
            });
        }

        self.pageLogs.push(msgStack.join('\n'));
    };

    if (VERBOSE) {
        this.webpage.onResourceReceived = function (response) {
            self.pageLogs.push('Response (#' + response.id + ', stage "' + response.stage + '", size "' +
                               response.bodySize + '", status "' + response.status + '"): ' + response.url);
        };
    }

    this.webpage.onResourceError = function (resourceError) {
        if (!self.aborted) {
            self.pageLogs.push('Unable to load resource (#' + resourceError.id + 'URL:' + resourceError.url + ')');
            self.pageLogs.push('Error code: ' + resourceError.errorCode + '. Description: ' + resourceError.errorString);
        }
    };

    this.webpage.onConsoleMessage = function (message) {
        self.pageLogs.push('Log: ' + message);
    };

    this.webpage.onAlert = function (message) {
        self.pageLogs.push('Alert: ' + message);
    };

    this.webpage.onLoadStarted = function () {
        self._isLoading = true;
    };

    this.webpage.onLoadFinished = function () {
        self._isLoading = false;
    };
};

PageRenderer.prototype.getPageContents = function () {
    var result = this.downloadedContents || this.webpage.content;

    if (/^<html><head><\/head><body>/.test(result)) {
        result = result.substring('<html><head></head><body>'.length);
    }

    if (/<\/body><\/html>$/.test(result)) {
        result = result.substring(0, result.length - '</body></html>'.length);
    }

    return result;
};

exports.PageRenderer = PageRenderer;
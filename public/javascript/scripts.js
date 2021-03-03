/**
 * shorthand for query selector
 * @param {string} selector
 * @returns {NodeList | Node | Element}
 */
function find(selector) {
    var result = document.querySelectorAll(selector.replace(/\//g, '\\/'));
    if (result.length === 1) {
        return result[0];
    }
    if (result.length === 0) {
        return false;
    }
    return result;
}

/**
 * @template T
 * @param {Array.<T>} array
 * @param {function(v: T, i: number, a: Array.<T>)} callback
 */
function forEach(array, callback) {
    if (array.forEach) {
        array.forEach(callback);
    } else {
        for (var i = 0; i < array.length; i++) {
            callback(array[i], i, array);
        }
    }
}

function stringRepeat(string, count) {
    if (string.repeat) {
        return string.repeat(count);
    }
    var str = '';
    for (var i = 0; i < count; i++) {
        str += string;
    }
    return str;
}

/**
 * shorthand for getBoundingClientRect
 * @param {HTMLElement} element
 */
function rect(element) {
    return element.getBoundingClientRect();
}

/**
 * creates an element with the given attributes and classnames
 * appends it to parent if provided
 * @param {string} tag
 * @param {Array.<{key: string, value: string}>} attributes
 * @param {string=} classNames
 * @param {Element=} parent
 * @returns {HTMLElement}
 */
function createElement(tag, attributes, classNames, parent) {
    var elem = document.createElement(tag);
    forEach(attributes, function (attr) {
        elem.setAttribute(attr.key, attr.value);
    });
    elem.className = classNames || '';
    if (parent) {
        parent.appendChild(elem);
    }
    return elem;
}

/**
 * shorthand for addEventListener
 * @param {Node} element
 * @param {string} event
 * @param {function} listener
 */
function on(element, event, listener) {
    if (element.addEventListener) {
        element.addEventListener(event, listener);
    } else {
        forEach(element, function (elem) {
            elem.addEventListener(event, listener);
        });
    }
}

var pushStateListeners = [];
function addPushStateEventListener(listener) {
    pushStateListeners.push(listener);
}

/**
 * triggers a history push state and fires registered push state listeners
 * @param {{}} pushStateOptions
 * @param {string} title
 * @param {string} url
 */
function triggerPushState(pushStateOptions, title, url) {
    window.history.pushState(pushStateOptions, title, url);
    forEach(pushStateListeners, function (cb) { cb(url); });
}

/**
 * executes ajax requests, invokes onFinished in case of status 200, otherwise on error, always onProgress
 * @param {string} url
 * @param {string} method
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onFinished
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onError
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onProgress
 * @param {*} data
 */
function ajax(url, method, onFinished, onError, onProgress, data) {
    var x = new XMLHttpRequest();
    x.open(method, url);
    x.onreadystatechange = function () {
        onProgress && onProgress.apply(x, [].concat([x], arguments));
        if (x.readyState === 4) {
            if (x.status === 200 && onFinished) {
                onFinished.apply(x, [].concat([x], arguments));
            } else if (x.status !== 200) {
                if (onError) {
                    onError.apply(x, [].concat([x], arguments));
                } else {
                    console.error('Error during ajax request: ', { status: x.status, readyState: x.readyState, url: url, method: method })
                }
            }
        }
    }
    x.send(data);
}

var jQueryListeners = [];
/**
 * adds a listener to be called as soon as jquery is initialized,
 * or immediately if initialization is already done
 * @param {function} listener
 */
function addJQueryListener(listener) {
    if (window.jQuery) {
        listener(window.jQuery);
    } else {
        jQueryListeners.push(listener);
    }
}

(function initJqueryAvailability() {
    var jq;
    // wait for jquery to be defined
    Object.defineProperty(window, 'jQuery', {
        set: function (jQuery) {
            jq = jQuery;
            forEach(jQueryListeners, function (listener) {
                listener(jQuery);
            });
        },
        get: function () {
            return jq;
        }
    });
})();

(function mobileMenuFunctions() {
    var openMenuButton = find('#open-mobile-menu');
    var menu = find('#main-menu');

    if (openMenuButton && menu) {
        on(openMenuButton, 'click', function () {
            menu.classList.toggle('open');
        });
    }

    var subMenuButtons = find('.show-sub-items');
    forEach(subMenuButtons, function (button) {
        on(button, 'click', function () {
            button.nextElementSibling.classList.toggle('open');
        });
    });
})();

(function infinityHeader() {
    var collageImages = find('.collage img');
    if (!collageImages) {
        return;
    }
    var firstCollageImageX = rect(collageImages[0]).x;
    var secondCollageImageX = rect(collageImages[1]).x;
    var singleImageWidth = secondCollageImageX - firstCollageImageX;
    var collage = find('.collage');
    var counter = 0;
    var curWidth = rect(collage).width;

    function extendHeader() {
        curWidth = rect(collage).width;
        while (curWidth > 0 && singleImageWidth > 0 && curWidth >= collageImages.length * singleImageWidth) {
            var newImg = document.createElement('img');
            var imgId = counter++ % collageImages.length;
            // don't re-render the logo
            if (imgId === 1) {
                imgId++;
                counter++;
            }
            newImg.setAttribute('src', collageImages[imgId].getAttribute('src'));
            collage.appendChild(newImg);
            collageImages = find('.collage img');
        }
    }
    // run once on startup
    extendHeader();
    on(window, 'resize', extendHeader);
})();

(function stickyMenu() {
    var navBar = find('header');
    var nav = find('.navbar');
    var collage = find('.collage');
    if (!collage || !nav || !navBar) {
        return;
    }
    var stickyNavClass = 'sticky-nav';
    var collageStyle = window.getComputedStyle(collage);
    var defaultCollageMargin = collageStyle.marginBottom;
    var defaultCollagePadding = collageStyle.paddingBottom;
    on(window, 'scroll', function () {
        var collage = find('.collage');
        var collageHeight = rect(collage).height;
        var navHeight = rect(nav).height;
        if (window.scrollY > collageHeight) {
            navBar.classList.add(stickyNavClass);
            collage.style.paddingBottom = 0;
            collage.style.marginBottom = navHeight + 'px';
        } else {
            navBar.classList.remove(stickyNavClass);
            collage.style.paddingBottom = defaultCollagePadding;
            collage.style.marginBottom = defaultCollageMargin;
        }
    });
})();

(function backToTopButton() {
    var backToTopButton = find('.back-to-top');
    if (!backToTopButton) {
        return;
    }
    on(window, 'scroll', function () {
        if (window.scrollY > window.innerHeight / 2) {
            backToTopButton.classList.add('revealed');
        } else {
            backToTopButton.classList.remove('revealed');
        }
    });

    function updateBackToTopButton() {
        var link = location.href;
        if (link.indexOf('#anchor-top') === -1) {
            link += '#anchor-top';
        }
        backToTopButton.setAttribute('href', link);
    }

    addPushStateEventListener(updateBackToTopButton);
    on(window, 'popstate', updateBackToTopButton);
    on(backToTopButton, 'click', function () {
        window.scrollTo({
            behavior: "smooth",
            top: 0
        });
    });
})();

(function dateLocalizationFix() {
    addJQueryListener(function (jQuery) {
        if (!jQuery.validator) {
            return;
        }
        jQuery(function () {
            jQuery.extend(jQuery.validator.methods, {
                date: function (a, b) {
                    return this.optional(b) || /^\d\d?\d?\d?[.\-/]\d\d?[.\-/]\d\d\d?\d?$/.test(a)
                }
            });
        });
    })
})();

/**
 * fixes sizing of video elements
 * affects videos in slider block + oembed block
 */
(function initializeVideoResizing() {
    var videos = find('.resized.type-video');
    resizeVideos(videos);

    on(window, 'resize', function() {
        resizeVideos(videos);
    });
    on(window, 'sliderready', function() {
        resizeVideos(videos);
    });

    function resizeVideos(videos) {
        forEach(videos, function (video) {
            var baseWidth = Number(video.getAttribute('data-width'));
            var baseHeight = Number(video.getAttribute('data-height'));
            var currentWidth = rect(video).width;

            var newWidth;
            var newHeight;

            if (currentWidth > baseWidth) {
                newWidth = baseWidth;
                newHeight = baseHeight;
            } else {
                newWidth = currentWidth;
                newHeight = baseHeight / baseWidth * currentWidth;
            }

            video.style.height = newHeight+"px";
        });
    }
})();

(function ajaxForms() {
    function enableInputFields(form) {
        forEach(form.querySelectorAll('input'), function(input) {
            input.removeAttribute('disabled');
        });
    }
    function disableInputFields(form) {
        forEach(form.querySelectorAll('input'), function(input) {
            input.setAttribute('disabled', 'disabled');
        });
    }

    function setupAjaxForm(form) {
        on(form, 'submit', function (e) {
            e.preventDefault();

            var method = form.getAttribute('method');
            var action = form.getAttribute('action');
            var data = new FormData(form);

            var formContainer = form.parentElement;

            disableInputFields(form);

            ajax(
                action + "?ajax=1",
                method,
                function(xhr) {    // success
                    formContainer.innerHTML = xhr.response;
                    var ajaxForm = formContainer.querySelector('.ajax-form');
                    setupAjaxForm(ajaxForm);
                    formContainer.dispatchEvent(new Event('formrebuild'));
                },
                function(xhr) {    // error
                    alert("Bei der Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.");
                    enableInputFields(form);
                },
                function(xhr) {    // progress
                },
                data
            );
        })
    }

    var ajaxForms = find('.ajax-form');
    if (ajaxForms) {
        if (ajaxForms instanceof Element) {
            setupAjaxForm(ajaxForms);
        } else {
            forEach(ajaxForms, setupAjaxForm);
        }
    }
})();

(function initPopups() {
    function initPopup(popup) {
        var closeBtns = popup.querySelectorAll('.close-popup');
        forEach(closeBtns, function(btn) {
            on(btn, 'click', function(e) {
                e.preventDefault();
                popup.classList.add('hidden');
            });
        });
    }

    var popups = find('.popup');
    if (popups) {
        if (popups instanceof Element) {
            initPopup(popups);
        } else {
            forEach(popups, initPopup);
        }
    }
})();

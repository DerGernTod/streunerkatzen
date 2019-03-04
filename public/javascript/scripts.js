/**
 * shorthand for query selector
 * @param {string} selector
 */
function find(selector) {
    var result = document.querySelectorAll(selector);
    if (result.length === 1) {
        return result[0];
    }
    return result;
}

/**
 * shorthand for getBoundingClientRect
 * @param {HTMLElement} element
 */
function rect(element) {
    return element.getBoundingClientRect();
}

/**
 * shorthand for addEventListener
 * @param {Node} element
 * @param {string} event
 * @param {function} listener
 */
function on(element, event, listener) {
    element.addEventListener(event, listener);
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
    pushStateListeners.forEach(function (cb) { cb(url); });
}

/**
 * executes ajax requests, invokes onFinished in case of status 200, otherwise on error, always onProgress
 * @param {string} url
 * @param {string} method
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onFinished
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onError
 * @param {function(x: XMLHttpRequest, e: ReadyStateChangeEvent)} onProgress
 */
function ajax(url, method, onFinished, onError, onProgress) {
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
    x.send();
}

/**
 * updates the html upon receiving a search result (e.g. through a search query or a page change)
 * @param {string} url
 * @param {boolean} doPushState
 * @param {string} content
 */
function handleSearchUpdate(url, doPushState, content) {
    find('.search-results').outerHTML = content;
    if (doPushState) {
        url = url.replace(/[\?&]ajax=1/g, '');
        triggerPushState(
            { url: url },
            document.title,
            url
        );
    }
    window.scrollTo({top: 0});
    addAjaxPagination();
}
/**
 * adds ajax pagination functionality to current pagination anchor tags
 */
function addAjaxPagination() {
    var pageLinks = find('.pagination a');
    if (!pageLinks.length) {
        return;
    }
    pageLinks.forEach(function (link) {
        on(link, 'click', function (e) {
            e.preventDefault();
            var url = this.getAttribute('href');
            paginate(url, true);
        });
    });
}
/**
 * sends an ajax request to load the next page of this list
 * @param {string} url
 * @param {boolean} doPushState
 * @param {function()} onComplete
 */
function paginate(url, doPushState, onComplete) {
    if (url.indexOf('ajax=1') < 0) {
        url += (url.indexOf('?') >= 0 ? '&' : '?') + 'ajax=1';
    }
    ajax(url, 'GET', function (x) {
        handleSearchUpdate(url, doPushState, x.response);
        onComplete && onComplete();
    }, function (x) {
        alert('Fehler: ' + x.responseText);
        onComplete && onComplete();
    });
}

(function mobileMenuFunctions() {
    var openMenuButton = find('#open-mobile-menu');
    var menu = find('#main-menu');

    if (openMenuButton && menu) {
        on(openMenuButton, 'click', function() {
            menu.classList.toggle('open');
        });
    }

    var subMenuButtons = find('.show-sub-items');
    subMenuButtons.forEach(function(button) {
        on(button, 'click', function() {
            button.nextElementSibling.classList.toggle('open');
        });
    });
})();
(function infinityHeader() {
    var collageImages = find('.collage img');

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
    var stickyNavClass = 'sticky-nav';
    var collageStyle = window.getComputedStyle(find('.collage'));
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
    on(window, 'scroll', function () {
        if (window.scrollY > window.innerHeight / 2) {
            backToTopButton.classList.add('revealed');
        } else {
            backToTopButton.classList.remove('revealed');
        }
    });
    addPushStateEventListener(function () {
        var link = location.href;
        if (link.indexOf('#anchor-top') === -1) {
            link += '#anchor-top';
        }
        backToTopButton.setAttribute('href', link);
    })
})();
(function asyncPagination() {
    var paginationElements = find('.pagination');
    if (!paginationElements.length && !paginationElements.nodeName) {
        return;
    }
    addAjaxPagination();
    on(window, 'popstate', function(e) {
        if (e.state && e.state.url) {
            paginate(e.state.url);
        }
    });
})();
(function asyncSearch() {
    var form = find('#Form_CatSearchForm');
    on(form, 'submit', function (e) {
        e.preventDefault();
        form.querySelectorAll('input').forEach(function (input) { input.setAttribute('disabled', 'disabled'); } );
        paginate(
            location.pathname
            + '?SearchValue='
            + find('#Form_CatSearchForm_SearchValue').value,
            true,
            function () {
                form.querySelectorAll('input').forEach(function (input) {
                    input.removeAttribute('disabled', 'disabled');
                });
            });
    });
})();

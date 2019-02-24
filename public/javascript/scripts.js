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
    })
})();

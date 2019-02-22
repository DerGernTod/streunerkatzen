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

(function mobileMenuFunctions() {
    var openMenuButton = find('#open-mobile-menu');
    var menu = find('#main-menu');

    if (openMenuButton && menu) {
        openMenuButton.addEventListener('click', function() {
            menu.classList.toggle('open');
        });
    }

    var subMenuButtons = find('.show-sub-items');
    subMenuButtons.forEach(function(button) {
        button.addEventListener('click', function() {
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
        while (curWidth >= collageImages.length * singleImageWidth) {
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
    window.addEventListener('resize', extendHeader);
})();
(function stickyMenu() {
    var navBar = find('header');
    var stickyNavClass = 'sticky-nav';
    var defaultCollageMargin = window.getComputedStyle(find('.collage')).marginBottom;
    window.addEventListener('scroll', function () {
        var collage = find('.collage');
        var collageHeight = rect(collage).height;
        var navBarHeight = rect(navBar).height;
        if (window.scrollY > collageHeight) {
            navBar.classList.add(stickyNavClass);
            collage.style.marginBottom = navBarHeight + 'px';
        } else {
            navBar.classList.remove(stickyNavClass);
            collage.style.marginBottom = defaultCollageMargin;
        }
    });
})();

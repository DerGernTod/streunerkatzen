(function() {
    // mobile menu functions
    var openMenuButton = document.getElementById('open-mobile-menu');
    var menu = document.getElementById('main-menu');

    if (openMenuButton && menu) {
        openMenuButton.addEventListener('click', function() {
            menu.classList.toggle('open');
        });
    }

    var subMenuButtons = document.querySelectorAll('.show-sub-items');
    subMenuButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            button.nextElementSibling.classList.toggle('open');
        });
    });
})();
(function() {
    var collageImages = document.querySelectorAll('.collage img');

    var firstCollageImageX = collageImages[0].getBoundingClientRect().x;
    var secondCollageImageX = collageImages[1].getBoundingClientRect().x;
    var singleImageWidth = secondCollageImageX - firstCollageImageX;
    var collage = document.querySelector('.collage');
    var counter = 0;
    var curWidth = collage.getBoundingClientRect().width;

    function extendHeader() {
        curWidth = collage.getBoundingClientRect().width;
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
            collageImages = document.querySelectorAll('.collage img');
        }
    }
    // run once on startup
    extendHeader();
    window.addEventListener('resize', extendHeader);
})();

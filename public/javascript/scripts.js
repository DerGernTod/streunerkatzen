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

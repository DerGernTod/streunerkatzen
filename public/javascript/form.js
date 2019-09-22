(function removeWeirdJsAlign() {
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.target.removeAttribute('style');
        });
    });
    var allJs = find('.js-align');
    allJs.forEach(function (element) {
        observer.observe(element, {attributes: true});
    });
})();

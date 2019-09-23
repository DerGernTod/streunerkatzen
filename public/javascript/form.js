(function removeWeirdJsAlign() {
    var observer = new MutationObserver(function (mutations) {
        forEach(mutations, function (mutation) {
            mutation.target.removeAttribute('style');
        });
    });
    var allJs = find('.js-align');
    forEach(allJs, function (element) {
        observer.observe(element, {attributes: true});
    });
})();

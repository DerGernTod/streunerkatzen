(function initBlog() {
    var btn = find('#load-more-btn');
    on(btn, 'click', function() {
        var offset = btn.getAttribute('data-offset');
        ajax(`${location.href}?offset=${offset}&ajax=1`, 'GET', function (xhr) {
            var div = document.createElement('div');
            find('.article-list').appendChild(div);
            div.outerHTML = xhr.responseText;
            btn.setAttribute('data-offset', xhr.getResponseHeader('x-offset'));
            if (Number(xhr.getResponseHeader('x-articles-left')) <= 0) {
                btn.parentElement.removeChild(btn);
            }
        });
    });
})();

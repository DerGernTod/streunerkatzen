(function initBlog() {
    var btns = find('.load-more-btn');
    if (btns) {
        if (btns instanceof Element) {
            setupLoadMore(btns);
        } else {
            forEach(btns, setupLoadMore);
        }
    }

    function setupLoadMore(btn) {
        on(btn, 'click', function() {
            var offset = btn.getAttribute('data-offset');
            var url = btn.getAttribute('data-url');

            ajax(url + '?offset=' + offset + '&ajax=1', 'GET', function (xhr) {
                var div = document.createElement('div');

                btn.parentElement.querySelector('.article-list').appendChild(div);

                div.outerHTML = xhr.responseText;

                btn.setAttribute('data-offset', xhr.getResponseHeader('x-offset'));

                if (Number(xhr.getResponseHeader('x-articles-left')) <= 0) {
                    btn.parentElement.removeChild(btn);
                }
            });
        });
    }
})();

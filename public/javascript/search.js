/**
 * replaces the current content of the search result, and enables the newly created pagination
 * @param {string} newContent
 */
function replaceSearchContent(newContent) {
    find('.search-results').outerHTML = newContent;
    ajaxifyPagination();
}

/**
 * sets disabled attribute of search form input fields
 * @param {boolean} enabled
 */
function setInputEnabled(enabled) {
    find('#Form_CatSearchForm').querySelectorAll('input').forEach(function (input) {
        if (enabled) {
            input.removeAttribute('disabled', 'disabled');
        } else {
            input.setAttribute('disabled', 'disabled');
        }
    });
}

/**
 * executes an ajax search. shows an alert popup on error, or replaces search-result content
 * and pushes history state
 * @param {string} targetUrl
 */
function executeSearch(targetUrl) {
    ajax(targetUrl + '&ajax=1', 'GET', function (xhr) {
        replaceSearchContent(xhr.response);
        window.scrollTo({top: 0});
        triggerPushState({ searchResult: xhr.response }, document.title, targetUrl);
        setInputEnabled(true);
    }, function () {
        alert('Fehler bei der Anfrage!');
        setInputEnabled(true);
    });
}

/**
 * registers a click event on all pagination anchors that triggers a search xhr to it's href attribute
 */
function ajaxifyPagination() {
    find('.pagination a').forEach(function (anchor) {
        on(anchor, 'click', function (e) {
            e.preventDefault();
            window.scrollTo({top: 0});
            setInputEnabled(false);
            var targetUrl = e.target.getAttribute('href').replace(/ajax=1/, '');
            executeSearch(targetUrl);
        });
    });
}

(function asyncSearch() {
    var initContent = find('.search-results').outerHTML;

    // ajaxify form submit
    on(find('#Form_CatSearchForm'), 'submit', function (e) {
        e.preventDefault();
        setInputEnabled(false);
        var targetUrl = location.pathname
            + '?SearchValue='
            + find('#Form_CatSearchForm_SearchValue').value;
        executeSearch(targetUrl);
    });

    on(window, 'popstate', function(e) {
        if (e.state && e.state.searchResult) {
            replaceSearchContent(e.state.searchResult);
        } else {
            replaceSearchContent(initContent);
        }
    });

    ajaxifyPagination();
})();

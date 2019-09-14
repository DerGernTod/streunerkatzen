var curAjax;
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
    if (curAjax) {
        curAjax.abort();
    }
    curAjax = ajax(targetUrl + '&ajax=1', 'GET', function (xhr) {
        replaceSearchContent(xhr.response);

        window.scrollTo({top: find('.search-results').offsetTop});
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
    var pagination = find('.pagination a');
    if (!pagination) {
        return;
    }
    pagination.forEach(function (anchor) {
        on(anchor, 'click', function (e) {
            e.preventDefault();
            window.scrollTo({top: 0});
            setInputEnabled(false);
            var targetUrl = e.target.getAttribute('href').replace(/[?&]?ajax=1/, '');
            executeSearch(targetUrl);
        });
    });
}

function preSelectInputs() {
    location.search.substr(1).split('&')
    .map(function (elem) {
        return elem.split('=');
    })
    .forEach(function (keyValPair) {
        var match = /(.*)\[[0-9].*\]/.exec(keyValPair[0]);
        if (match && match.length > 1) {
            var key = match[1];
            var val = decodeURIComponent(keyValPair[1]);
            var inputElem = find('#filter-field-' + key + '-' + val);
            if (inputElem) {
                inputElem.setAttribute('checked', 'checked');
            }
        }
    });

}

function buildUrlAndSearch() {
    setInputEnabled(false);
    var filters = {};
    var filteredElems = find('.filter-field:checked');
    function pushFilter(input) {
        var match = /filter-field-([A-Za-z]*)-(.*)/g.exec(input.id);
        if (match.length != 3) {
            return;
        }
        if (!filters[match[1]]) {
            filters[match[1]] = [];
        }
        filters[match[1]].push(match[2]);
    }
    if (filteredElems.forEach) {
        filteredElems.forEach(pushFilter);
    } else if (filteredElems) {
        pushFilter(filteredElems);
    }
    var filterParams = [];
    for (var key in filters) {
        filters[key].forEach(function(item, index) {
            filterParams.push(key + '[' + index + ']=' + encodeURIComponent(item));
        });
    }

    var targetUrl = location.pathname
        + '?SearchTitle='
        + find('#Form_CatSearchForm_SearchTitle').value
        + '&'
        + filterParams.join('&');
    executeSearch(targetUrl);
}

(function asyncSearch() {
    var searchResults = find('.search-results');
    if (!searchResults) {
        return;
    }
    var initContent = searchResults.outerHTML;

    // ajaxify form submit
    on(find('#Form_CatSearchForm'), 'submit', function (e) {
        e.preventDefault();
        buildUrlAndSearch();
    });
    find('input[type="checkbox"],input[type="radio"]').forEach(function (filterElem) {
        on(filterElem, 'change', function (e) {
            buildUrlAndSearch();
        });
    });

    on(window, 'popstate', function(e) {
        if (e.state && e.state.searchResult) {
            replaceSearchContent(e.state.searchResult);
        } else {
            replaceSearchContent(initContent);
        }
    });

    ajaxifyPagination();
    preSelectInputs();
})();

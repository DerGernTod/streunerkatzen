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
    forEach(find('#Form_CatSearchForm').querySelectorAll('input'), function (input) {
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
    setInputEnabled(false);
    if (curAjax) {
        curAjax.abort();
    }
    curAjax = ajax(targetUrl + '&ajax=1', 'GET', function (xhr) {
        replaceSearchContent(xhr.response);
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
    forEach(pagination, function (anchor) {
        on(anchor, 'click', function (e) {
            e.preventDefault();
            window.scrollTo({top: 0});
            setInputEnabled(false);
            var targetUrl = e.target.getAttribute('href').replace(/[?&]?ajax=1/, '');
            executeSearch(targetUrl);
        });
    });
}

/**
 * enables filter checkboxes depending on the query string of the url
 * @returns {boolean} true if any filters have been set
 */
function preSelectInputs() {
    var didSelectFilters = false;
    location.search.substr(1).split('&')
    .map(function (elem) {
        return elem.split('=');
    })
    .forEach(function (keyValPair) {
        var match = /(.*)\[[0-9].*\]/.exec(keyValPair[0]);
        console.log('preselecting', keyValPair);
        if (match && match.length > 1) {
            var key = match[1];
            var val = decodeURIComponent(keyValPair[1]);
            var inputElem = find('#filter-field-' + key + '-' + val.replace(/ /g, '\\ '));
            if (inputElem) {
                inputElem.setAttribute('checked', 'checked');
                didSelectFilters = true;
            }
        } else {
            var elem = find('#filter-field-' + keyValPair[0]);
            if (elem) {
                elem.value = keyValPair[1];
                didSelectFilters = true;
            }
        }
    });
    return didSelectFilters;
}

/**
 * pushes a checked input field to the list of filtered values
 * @param {HTMLElement} input a checked checkbox element
 * @param {object.<string, string[]>} filters a map of filters
 */
function pushFilter(input, filters) {
    var match = /filter-field-([A-Za-z]*)-(.*)/g.exec(input.id);
    if (match.length != 3) {
        return;
    }
    if (!filters[match[1]]) {
        filters[match[1]] = [];
    }
    filters[match[1]].push(match[2]);
}

/**
 * constructs a url for filtering cats and triggers a search
 */
function buildUrlAndSearch() {
    var filters = {};
    // filter on checkboxes and radios
    var filteredElems = find('.filter-field:checked');
    if (filteredElems.length) {
        forEach(filteredElems, function(elem) {
            pushFilter(elem, filters);
        });
    } else if (filteredElems) {
        pushFilter(filteredElems, filters);
    }

    var filterParams = [];
    var hasFilters = false;
    for (var key in filters) {
        forEach(filters[key], function(item, index) {
            if (item !== 'nicht bekannt') {
                hasFilters = true;
            }
            filterParams.push(key + '[' + index + ']=' + encodeURIComponent(item));
        });
    }
    // filter on date
    var from = find('#filter-field-LostFoundDate-from').value;
    var to = find('#filter-field-LostFoundDate-to').value;
    var dateRegex = /[0-9]{4}(-[0-9]{2}){2}/;
    var gotInvalidDate = false;
    if (from) {
        gotInvalidDate = !dateRegex.exec(from);
        filterParams.push('LostFoundDate-from=' + encodeURIComponent(from));
        hasFilters = true;
    }
    if (to) {
        gotInvalidDate = gotInvalidDate || !dateRegex.exec(to);
        filterParams.push('LostFoundDate-to=' + encodeURIComponent(to));
        hasFilters = true;
    }
    if (from && to && from > to) {
        gotInvalidDate = true;
    }
    if (gotInvalidDate) {
        find('#date-error-message').classList.remove('hidden');
        return;
    }
    find('#date-error-message').classList.add('hidden');
    var searchValue = find('#Form_CatSearchForm_SearchTitle').value;
    if (hasFilters || searchValue) {
        find('#search-agent').parentElement.classList.remove('hidden');
    } else {
        find('#search-agent').parentElement.classList.add('hidden');
    }
    var targetUrl = location.pathname
        + '?SearchTitle='
        + searchValue
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

    var agentPopup = find('#agent-popup');
    var agentPopupForm = find('#agent-popup>form');
    var emailField = find('#agent-email');
    var emailTemplateField = find('#agent-mail-template');
    // ajaxify form submit
    on(find('#Form_CatSearchForm'), 'submit', function (e) {
        e.preventDefault();
        buildUrlAndSearch();
    });
    forEach(find('input'), function (filterElem) {
        if (filterElem === emailField) {
            return;
        }
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
    var filtersSelected = preSelectInputs();
    if (filtersSelected) {
        buildUrlAndSearch();
    }
    on(find('#filter-field-LostFoundDate-from, #filter-field-LostFoundDate-to'), 'focus', function (e) {
        find('#date-error-message').classList.add('hidden');
    });
    on(find('#search-agent'), 'click', function (e) {
        e.preventDefault();
        agentPopup.classList.remove('hidden');
    });
    on(agentPopupForm, 'submit', function (e) {
        e.preventDefault();
        var inputs = find('input');
        forEach(inputs, function (input) {
            input.setAttribute('disabled', 'disabled');
        });
        var formData = new FormData();
        formData.append('email', emailField.value);
        formData.append('email-template', emailTemplateField.value);
        ajax(location.pathname + 'agent' + location.search, 'POST', function (xhr) {
            var div = createHint(xhr.response);
            agentPopup.appendChild(div);
            setTimeout(function () {
                forEach(inputs, function (input) {
                    input.removeAttribute('disabled', 'disabled');
                });
                agentPopup.classList.add('hidden');
                div.parentElement.removeChild(div);
                emailField.value = '';
            }, 3500);
        }, function (xhr) {
            alert(xhr.response);
            forEach(inputs, function (input) {
                input.removeAttribute('disabled', 'disabled');
            });
        }, void 0, formData);
    });
    on(agentPopupForm, 'reset', function (e) {
        agentPopup.classList.add('hidden');
    });
})();

(function catMessageSending() {
    var msgForm = find('#Form_SendMessageForm');
    var sendBtn = find('#Form_SendMessageForm_action_sendMessage');
    var area = find('#Form_SendMessageForm_cat-msg');
    var secId = find('#Form_SendMessageForm_SecurityID');
    var catId = find('#Form_SendMessageForm_cat-id');
    on(msgForm, 'submit', function (e) {
        e.preventDefault();
        // todo: captcha
        sendBtn.setAttribute('disabled', 'disabled');
        area.setAttribute('disabled', 'disabled');
        var data = new FormData();
        data.set('catId', catId.value);
        data.set('text', area.value);
        data.set('SecurityID', secId.value);
        ajax(this.action, 'POST', function () {
            sendBtn.removeAttribute('disabled');
            area.removeAttribute('disabled');
            area.value = '';
            alert('Nachricht gesendet!');
        }, function () {
            sendBtn.removeAttribute('disabled');
            area.removeAttribute('disabled');
            alert('Fehler beim Senden der Nachricht!');
        }, void 0, data);
    });
})();

/**
 * creates a div element with a message to be attached to any parent
 * @param {string} message
 */
function createHint(message, classes) {
    var div = document.createElement('div');
    div.innerHTML = message;
    if (classes) {
        div.classList.add(classes);
    }
    return div;
}

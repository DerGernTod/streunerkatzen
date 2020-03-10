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
(function buildListbox() {
    var listbox = find('select.listbox');
    listbox.classList.add('hidden');
    var chosen = createElement('div', [], 'chosen', listbox.parentElement);
    var options = find('select.listbox option');

    var resultUl = createElement('ul', [], 'chosen-choices', chosen);
    var inputLi = createElement('li', [], 'chosen-search-field', resultUl);
    var input = createElement('input', [{key: 'type', value: 'text'}], 'chosen-search-input', inputLi);

    var dropdown = createElement('div', [], 'chosen-drop', chosen);
    var chosenOptions = createElement('ul', [], 'chosen-options', dropdown);
    var chosenOptionLis = [];
    var closeDisabled = false;
    var removeQueue = [];

    setDropdownVisible(false);

    on(chosen, 'click', function () {
        input.focus();
        setDropdownVisible(true);
    });

    on(input, 'keydown', function (e) {
        var prevInput = input.value;
        if (e.key.toUpperCase() === 'ENTER') {
            e.preventDefault();
        }
        setTimeout(function () {
            var val = input.value;
            if (val) {
                setDropdownVisible(true);
            }
            forEach(chosenOptionLis, function (li) {
                if (!val || li.innerText.toLowerCase().indexOf(val.toLowerCase()) >= 0) {
                    li.classList.remove('hidden');
                } else {
                    li.classList.add('hidden');
                }
            });
            if (!val && !prevInput && removeQueue.length > 0) {
                removeQueue.pop()(e);
            }
        }, 0);
    });

    on(chosen, 'mouseenter', function () {
        closeDisabled = true;
    });

    on(chosen, 'mouseleave', function () {
        closeDisabled = false;
    });

    on(input, 'blur', function () {
        if (!closeDisabled) {
            input.value = '';
            setDropdownVisible(false);
        }
    });

    function setDropdownVisible(visible) {
        if (visible) {
            dropdown.classList.remove('hidden');
            forEach(chosenOptionLis, function (li) {
                li.classList.remove('hidden');
            });
        } else {
            dropdown.classList.add('hidden');
        }
    }

    /**
     *
     * @param {HTMLOptionElement} originalOption
     * @param {HTMLLIElement} chosenOption
     */
    function addOption(originalOption, chosenOption) {
        originalOption.selected = true;
        chosenOption.classList.remove('selectable');
        var optionLi = createElement('li', [], 'chosen-selected-choice');
        var text = createElement('span', [], '', optionLi);
        text.innerText = originalOption.value;
        var remove = createElement('a', [{key: 'href', value: 'javascript: void 0'}], 'chosen-remove', optionLi);
        remove.innerText = '❌';
        removeQueue.push(removeSelected);
        function removeSelected(e) {
            e.stopPropagation();
            resultUl.removeChild(optionLi);
            removeOption(originalOption, chosenOption);
            var removeQueueIndex = removeQueue.indexOf(removeSelected);
            if (removeQueueIndex >= 0) {
                removeQueue.splice(removeQueueIndex, 1);
            }
        }
        on(remove, 'click', removeSelected);
        resultUl.insertBefore(optionLi, inputLi);
        input.value = '';
        setDropdownVisible(false);
        input.focus();
    }

    /**
     * @param {HTMLOptionElement} originalOption
     * @param {HTMLLIElement} chosenOption
     */
    function removeOption(originalOption, chosenOption) {
        originalOption.selected = false;
        chosenOption.classList.add('selectable');
        setDropdownVisible(false);
    }

    forEach(options, function(option) {
        var optionEl = createElement('li', [{ key: 'data-li', value: option.value }], 'chosen-option selectable', chosenOptions);
        optionEl.innerText = option.value;
        on(optionEl, 'click', function (e) {
            e.stopPropagation();
            if (optionEl.classList.contains('selectable')) {
                addOption(option, optionEl);
            }
        });
        chosenOptionLis.push(optionEl);
    });


})();

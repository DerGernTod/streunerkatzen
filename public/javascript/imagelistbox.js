
(function initImageListbox() {
    var containers = document.querySelectorAll('.multi-image-select-container'.replace(/\//g, '\\/'));
    if (containers.length === 0) {
        return;
    }

    containers.forEach(function(container) {
        var select = container.querySelector('select');
        var listbox = container.querySelector('.multi-image-select');
        var selected = listbox.querySelector('.selected-options');
        var options = listbox.querySelector('.options');
        var optionsList = options.querySelectorAll('.option');

        // open/close dropdown
        selected.addEventListener('click', function(event) {
            setVisibility(options, true);
            event.stopPropagation();

            window.addEventListener('click', (function windowClickListener(event) {
                setVisibility(options, false);
                window.removeEventListener('click', windowClickListener);
            }));
        });
        options.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // option selection
        optionsList.forEach(function(option) {
            var value = option.getAttribute('data-value');
            var optionElem = select.querySelector('option[value="'+value+'"]');
            var tagElem = selected.querySelector('div[data-value="'+value+'"]');
            tagElem.parentElement.removeChild(tagElem);
            var closeTag = tagElem.querySelector('.close');

            closeTag.addEventListener('click', toggleSelectedOption);

            option.addEventListener('click', toggleSelectedOption);

            function toggleSelectedOption(event) {
                event.stopPropagation();
                option.classList.toggle('selected');
                toggleOption(optionElem);
                toggleTag(tagElem, selected);
            }
        });

        listbox.classList.remove("hidden");

        function setVisibility(element, visible) {
            if (visible) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }

        function toggleOption(option) {
            var selected = option.getAttribute('selected');
            if (selected) {
                option.removeAttribute('selected');
            } else {
                option.setAttribute('selected', 'selected');
            }
        }
        function toggleTag(tag, parent) {
            if (tag.parentElement) {
                tag.parentElement.removeChild(tag);
            } else {
                parent.appendChild(tag);
            }
        }
    });
})();

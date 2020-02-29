(function catplugin() {
    var listbox = null;
    var catpluginEditor = null;
    var catWin = null;
    var currentAjax = null;
    var initListbox = {
        type: 'listbox',
        name: 'catselect',
        onPostRender: function() {
            if (listbox) {
                listbox.remove();
            }
            listbox = this;
        }
    };

    function loadSearchResult(elem) {
        if (currentAjax) {
            currentAjax.abort();
        }
        var xhr = new XMLHttpRequest();
        currentAjax = xhr;
        var value = elem.value();
        xhr.responseType = 'json';
        xhr.open('GET', '/streunerkatzen-silverstripe/api/catsearch?search=' + value);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var res = xhr.response;
                if (res && typeof res === 'object') {
                    initListbox.values = res.map(function (val) {
                        return { text: val.title + ' (' + val.publishTime + ')', value: val.id };
                    });
                    elem.parent().parent().append(initListbox);
                }
                currentAjax = null;
            }
        }
        xhr.send();
    }

    tinymce.PluginManager.add('catplugin', function(editor, url) {
        catpluginEditor = editor;
        // Add a button that opens a window
        editor.addButton('catplugin', {
            text: '🐱',
            tooltip: 'Katze einfügen',
            icon: false,
            onclick: function() {
                // Open window
                editor.windowManager.open({
                    title: 'Katze einfügen',
                    body: [
                        {
                            type: 'textbox',
                            label: 'Katze suchen',
                            name: 'catsearch',
                            onkeydown: function(e) {
                                var elem = this;
                                setTimeout(function() {
                                    loadSearchResult(elem);
                                }, 0);
                            }
                        }
                    ],
                    onsubmit: function(e) {
                        listbox = null;
                        catwin = null;
                        // Insert content when the window form is submitted
                        editor.insertContent('[cat,id=' + e.data.catselect + ']');
                    },
                    onPostRender: function() {
                        catWin = this;
                    }
                });
            }
        });
    });
})();

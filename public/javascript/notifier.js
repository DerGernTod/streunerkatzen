(function () {
    var progress = find('#noti-progress');
    var initProgressText = progress.textContent;
    var requestActive = false;
    function removeNotiText() {
        var text = find('#noti-text');
        text.parentElement.removeChild(text);
    }

    /**
     * adds leading zeroes to a number until size is reached
     * @param {number} num the number to add leading zeroes to
     * @param {number} size the expected string length
     */
    function pad(num, size) {
        var s = String(num);
        while (s.length < size) s = "0" + s;
        return s;
    }
    on(find('#noti-unsubscribe'), 'click', function () {
        if (confirm('Wirklich abbestellen?')) {
            var formData = new FormData();
            formData.set('token', this.getAttribute('data-token'));
            sendRequestUpdateProgress('./notifications/unsubscribe',
                formData,
                'Wir werden dich nicht mehr daran erinnern.',
                removeNotiText);
        }
    });
    on(find('#noti-delete'), 'click', function () {
        if (confirm('Wirklich löschen?')) {
            var formData = new FormData();
            formData.set('token', this.getAttribute('data-token'));
            sendRequestUpdateProgress('./notifications/delete',
                formData,
                'Die Katze wurde gelöscht und du wirst keine Erinnerung mehr zu ihr erhalten.',
                removeNotiText);
        }
    });
    on(find('#noti-timespan'), 'click', function () {
        var weeks = parseInt(prompt('In wievielen Wochen sollen wir dich wieder benachrichtigen?', '2'), 10);
        if (weeks > 0) {
            var formData = new FormData();
            formData.set('token', this.getAttribute('data-token'));
            formData.set('weeks', weeks);
            var time = new Date(Date.now() + 1000 * 60 * 60 * 24 * 7 * weeks);
            sendRequestUpdateProgress('./notifications/timespan',
                formData,
                'Wir werden dich in ' + weeks + ' Wochen noch einmal erinnern.',
                function () {
                    var span = find('#noti-date');
                    var day = pad(time.getDate(), 2);
                    var month = pad(time.getMonth() + 1, 2);
                    span.innerText = day + '.' + month + '.' + time.getFullYear();
                });
        } else {
            alert('Bitte nur positive Zahlen angeben!');
        }
    });

    /**
     * activates or deactivates the subscription links on this page
     * @param {boolean} enabled
     */
    function setLinksEnabled(enabled) {
        requestActive = !enabled;
        forEach(find('.noti-link'), function (elem) {
            if (enabled) {
                elem.setAttribute('href', 'javascript:void 0');
            } else {
                elem.removeAttribute('href');
            }
        });
    }

    /**
     * sends an xhr and updates the progress element and the clicked element
     * @param {string} url the target of the request
     * @param {string=} data optional data to be sent
     * @param {string=} message optional message to be displayed after success
     * @param {function=} callback optional callback to be called upon success
     */
    function sendRequestUpdateProgress(url, data, message, callback) {
        if (requestActive) {
            return;
        }
        setLinksEnabled(false);
        progress.classList.remove('hidden');
        progress.textContent = initProgressText;
        var dots = 0;
        var interval = setInterval(function () {
            progress.textContent = initProgressText + stringRepeat('.', dots % 4);
            dots++;
        }, 250);
        ajax(url, 'POST', function () {
            clearInterval(interval);
            progress.textContent = 'Einstellungen übernommen! ' + (message || '');
            setLinksEnabled(true);
            callback && callback();
        }, function () {
            clearInterval(interval);
            progress.textContent = 'Fehler beim Übernehmen der Einstellungen!';
            setLinksEnabled(true);
        }, void 0, data);
    }

})();

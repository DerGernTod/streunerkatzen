<h1>Benachrichtigungseinstellungen</h1>
<div>
    Du hast mit der Email-Adresse <em>$Cat.Contact</em> diese Katze bei uns eingetragen:
    <% with $Cat %>
    <% include Streunerkatzen/Includes/CatShortcodeView %>
    <% end_with %>
    <div id='noti-text'>
        Wir werden dich am <span id='noti-date'>$NextReminder.Format('dd.MM.y')</span> wieder daran erinnern.
    </div>
</div>
<ul class='noti-controls'>
    <li><a href='javascript:void 0' class='noti-link' id='noti-timespan' data-token='$EditToken'>Zeitraum wählen</a></li>
    <li><a href='javascript:void 0' class='noti-link' id='noti-unsubscribe' data-token='$EditToken'>Abbestellen</a></li>
    <li><a href='javascript:void 0' class='noti-link' id='noti-delete' data-token='$EditToken'>Katze löschen</a></li>
</ul>
<div id='noti-progress' class='hidden'>In Bearbeitung</div>

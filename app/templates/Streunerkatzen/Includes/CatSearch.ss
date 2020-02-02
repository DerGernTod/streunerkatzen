$Content
$CatSearchForm
<form class="filter-search-form">
    <div class='filter-group' class='hidden'>
        <a id='search-agent'>🔔 Benachrichtigung aktivieren</a> <span class='hint' title='$StrippedNotificationTemplate'>?</span>
    </div>
    <div id="filter-LostFoundDate" class="filter-group">
        <h2>Zeitpunkt</h2>
        <div id='date-error-message' class='hidden'>Bitte gültiges Datumsformat angeben: jjjj-mm-tt. "Von" muss früher als "Bis" sein.</div>
        <div class="filter-group-content">
            <label for="filter-field-LostFoundDate-from">Von</label>
            <input type="date" placeholder="jjjj-mm-tt" name="filter-field-LostFoundDate-from" id="filter-field-LostFoundDate-from"></input>
        </div>
        <div class="filter-group-content">
            <label for="filter-field-LostFoundDate-to">Bis</label>
            <input type="date" placeholder="jjjj-mm-tt" name="filter-field-LostFoundDate-to" id="filter-field-LostFoundDate-to"></input>
        </div>
    </div>
    <% loop getFilters() %>
        <div id="filter-$Title" class="filter-group">
            <h2>$Label</h2>
            <div class="filter-group-content-container">
                <div class="filter-group-content">
                    <% loop $Values %>
                        <input type="$Up.InputType" class="filter-field" name="filter-field-$Up.Title" id="filter-field-$Up.Title-$Text"></input>
                        <label for="filter-field-$Up.Title-$Text">$Text</label>
                    <% end_loop %>
                </div>
            </div>
        </div>
    <% end_loop %>

</form>

<div id='agent-popup' class='popup hidden'>
    <form>
        <h2>Benachrichtigung</h2>
        <div>
            $NotificationTemplate
        </div>
        <div>
            <label for='agent-email'>E-Mail</label>
            <input type='email' name='agent-email' id='agent-email'>
            <input type='hidden' id='agent-mail-template' value='$NotificationEmailTemplate'>
        </div>
        <div>
            <input type='submit' value='Speichern'>
            <input type='reset' value='Abbrechen'>
        </div>
    </form>
</div>
<% include Streunerkatzen/CatSearchResult %>

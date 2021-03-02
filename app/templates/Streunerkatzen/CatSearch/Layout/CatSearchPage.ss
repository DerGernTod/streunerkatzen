<div>
    $ElementalArea
    $CatSearchForm
    <form class="filter-search-form">
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
        <% loop Filters() %>
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
        <div>
            <h2>Benachrichtigung</h2>
            <div>
                $NotificationTemplate
            </div>
            <div class="form-container" id="notification-form-container">
                $NotificationForm
            </div>
            <% include Streunerkatzen/Button Label="Schließen", ID="search-agent-popup-close-button", Link="#", ExtraClasses="close-popup" %>
        </div>
    </div>

    <div class='notification-popup-button hidden'>
        <a id='search-agent'>🔔 Benachrichtigung aktivieren</a> <span class='hint' title='$StrippedNotificationTemplate'>?</span>
    </div>
    <% include Streunerkatzen/CatSearch/CatSearchResult %>
</div>

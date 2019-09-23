$Content
$CatSearchForm
<form class="filter-search-form">
    <div id="filter-LostFoundDate" class="filter-group">
        <h2>Zeitpunkt</h2>
        <div id='date-error-message' class='hidden'>Bitte gültiges Datumsformat angeben: tt-mm-jjjj.</div>
        <div class="filter-group-content">
            <label for="filter-field-LostFoundDate-from">Von</label>
            <input type="date" placeholder="tt-mm-jjjj" name="filter-field-LostFoundDate-from" id="filter-field-LostFoundDate-from"></input>
        </div>
        <div class="filter-group-content">
            <label for="filter-field-LostFoundDate-to">Bis</label>
            <input type="date" placeholder="tt-mm-jjjj" name="filter-field-LostFoundDate-to" id="filter-field-LostFoundDate-to"></input>
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

<% include Streunerkatzen/CatSearchResult %>

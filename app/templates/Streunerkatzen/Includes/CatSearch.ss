$Content
$CatSearchForm
<form class="filter-search-form">
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

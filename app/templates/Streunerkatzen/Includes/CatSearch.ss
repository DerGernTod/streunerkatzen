$Content
<form class="filter-search-form">
    <div id="filter-is-castrated" class="filter-group">
        <h2>Kastriert?</h2>
        <% loop getDropdownOptions('IsCastrated') %>
            <input type="radio" name="is-castrated" id="is-castrated-$Text"></input>
            <label for="is-castrated-$Text">$Text</label>
        <% end_loop %>
    </div>
    <div id="filter-hair-color" class="filter-group">
        <h2>Fellfarben</h2>
        <% loop getDropdownOptions('IsCastrated') %>
            <input type="checkbox" name="hair-color" id="hair-color-$Text"></input>
            <label for="hair-color-$Text">$Text</label>
        <% end_loop %>
    </div>
</form>#is-castrated-nicht\20 bekannt
$CatSearchForm

<% include Streunerkatzen/CatSearchResult %>

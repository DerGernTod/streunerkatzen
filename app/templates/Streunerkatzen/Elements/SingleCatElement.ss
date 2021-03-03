<% if $Title && $ShowTitle %><h2 class="element__title">$Title</h2><% end_if %>
<% if $Cat %>
    <% with $Cat %>
        <% include Streunerkatzen/CatSummary %>
    <% end_with %>
<% end_if %>

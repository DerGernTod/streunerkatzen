<div>
    <% if $Cat %>
        <% with $Cat %>
            <% include Streunerkatzen/CatPage %>
        <% end_with %>
    <% else %>
        <% include Streunerkatzen/CatSearch %>
    <% end_if %>
</div>

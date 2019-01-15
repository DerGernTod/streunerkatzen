<% loop $SortedComponents %>
    <% if $ClassName == "Streunerkatzen\TextComponent" %>
        <% include Streunerkatzen/TextComponent %>
    <% end_if %>
<% end_loop %>
<div class="clear"></div>

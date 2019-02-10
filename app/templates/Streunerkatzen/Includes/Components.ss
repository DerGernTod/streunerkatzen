<% loop $SortedComponents %>
    <% if $ClassName == "Streunerkatzen\ContentComponent" %>
        <% include Streunerkatzen/ContentComponent %>
    <% end_if %>
    <% if $ClassName == "Streunerkatzen\ButtonComponent" %>
        <% include Streunerkatzen/ButtonComponent %>
    <% end_if %>
<% end_loop %>
<div class="clear"></div>

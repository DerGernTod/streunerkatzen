<% loop $SortedComponents %>
    <% if $ClassName == "Streunerkatzen\NewLineComponent"  %>
        <% include Streunerkatzen/NewLineComponent %>
    <% else %>
        <div class="$GridSizeLarge $GridSizeMedium $GridSizeSmall">
            <% if $ClassName == "Streunerkatzen\ContentComponent" %>
                <% include Streunerkatzen/ContentComponent %>
            <% end_if %>
            <% if $ClassName == "Streunerkatzen\ButtonComponent" %>
                <% include Streunerkatzen/ButtonComponent %>
            <% end_if %>
            <% if $ClassName == "Streunerkatzen\SingleCatComponent" %>
                <% include Streunerkatzen/SingleCatComponent %>
            <% end_if %>
        </div>
    <% end_if %>
<% end_loop %>
<div class="clear"></div>

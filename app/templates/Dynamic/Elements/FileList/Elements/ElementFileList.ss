<% if $Title && $ShowTitle %><h2 class="element__title">$Title</h2><% end_if %>
<% if $Content %><div class="element__content">$Content</div><% end_if %>

<% if $Files %>
    <% loop $Files.Sort(SortOrder, ASC) %>
        <p><a href="$File.URL"><% if $Title %>$Title<% else %>$File.Title<% end_if %><br /><span>$File.Size $File.Extension.UpperCase</span></a></p>
    <% end_loop %>
<% end_if %>

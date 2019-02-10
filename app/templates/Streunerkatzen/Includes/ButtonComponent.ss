<a
    class='btn'
    <% if $IsNewWindow %>target='blank'<% end_if %>
    href='<% if $Page %>$Page.Link<% else %>$Link<% end_if %>'
    $Label
</a>


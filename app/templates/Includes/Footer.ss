<footer>
    <nav class="footer-nav">
        <% loop $MenuSet('Footer').MenuItems %>
            <a href="$Link" class="$LinkingMode" <% if $IsNewWindow %>target="_blank"<% end_if %>>$MenuTitle</a>
        <% end_loop %>
    </nav>
    <p>&copy;$CurrentYear <a href="$BaseHref">Streunerkatzen OÖ</a>
    </p>
</footer>

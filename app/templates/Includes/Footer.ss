<footer>
    <nav class="social">
        <a href="#">Blog</a>
        <a href="#">Facebook</a>
    </nav>
    <p>(c)2020 <a href="$BaseHref">Streunerkatzen OÖ</a>
        <% loop $MenuSet('Footer').MenuItems %>
            <a href="$Link">$MenuTitle</a>
        <% end_loop %>
    </p>
</footer>

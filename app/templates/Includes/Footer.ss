<div id="footer" class="menu">
    <% include SleepingCat %>
    <div class="container">
        <div class="main-items">
            <% loop $MenuSet('Footer').MenuItems %>
                <div class="item $LinkingMode">
                    <a href="$Link">$MenuTitle</a>
                </div>
            <% end_loop %>
        </div>
    </div>
</div>

<footer>
    <nav class="social">
        <a href="#">Blog</a>
        <a href="#">Facebook</a>
    </nav>
    <p>(c)2020 <a href="#">Streunerkatzen OÖ</a> <a href="#">Impressum</a></p>
</footer>

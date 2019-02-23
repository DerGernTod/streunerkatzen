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

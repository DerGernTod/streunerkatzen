<nav class="main-menu">
    <% loop $Menu(1) %>
        <div class="item $LinkingMode">
            <a href="$Link">$MenuTitle</a>
            <% if $Children %>
                <div class="show-sub-items"></div>
                <div class="sub-items">
                    <% loop $Children %>
                        <div class="item $LinkingMode">
                            <a href="$Link">$MenuTitle</a>
                        </div>
                    <% end_loop %>
                </div>
            <% end_if %>
        </div>
    <% end_loop %>
</nav>

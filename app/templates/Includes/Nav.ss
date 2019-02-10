<div id="main-menu" class="menu">
    <div class="navbar">
        <div class="container">
            <div class="main-items">
                <% loop $Menu(1) %>
                    <div class="item $LinkingMode">
                        <a href="$Link">$MenuTitle</a>
                        <% if $Children %>
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
            </div>
        </div>
    </div>
</div>

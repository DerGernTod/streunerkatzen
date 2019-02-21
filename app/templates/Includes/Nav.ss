<div id="main-menu" class="menu">
    <div class="navbar">
        <div class="container">
            <div id="open-mobile-menu"><div class="lines"></div></div>
            <div class="main-items">
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
                <% with $Page('Spenden') %>
                <a class="call-to-action btn item" href="$Link">Jetzt Spenden!</a>
                <% end_with %>
            </div>
        </div>
    </div>
</div>

<nav id="main-menu" class="main-menu">
    <% loop $Menu(1) %>
        <div class="item $LinkingMode <% if $Children %> has-sub-items<% end_if %>">
            <a href="$Link">$MenuTitle</a>
            <% if $Children %>
                <div class="show-sub-items"></div>
                <div class="sub-items">
                    <div class="sub-items-wrapper">
                        <% loop $Children %>
                            <div class="item $LinkingMode">
                                <a href="$Link">$MenuTitle</a>
                            </div>
                        <% end_loop %>
                    </div>
                </div>
            <% end_if %>
        </div>
    <% end_loop %>
</nav>

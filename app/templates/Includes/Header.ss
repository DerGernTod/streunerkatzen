<% with $SiteConfig %>
    <% if $LogoImage %>
        <div class="collage">
            <% loop $ShuffledCollage %>
            <% if $Pos == 2 %>
                <img src="$SiteConfig.LogoImage.Fill(200,200).URL">
            <% end_if %>
                <img src="$Fill(200,200).URL">
            <% end_loop %>
        </div>
    <% end_if %>
<% end_with %>
<header>
    <% include Nav %>
</header>

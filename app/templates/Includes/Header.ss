<header class="site-header" <% if $SiteConfig.HeaderImage %>style="background-image: url($SiteConfig.HeaderImage.URL)"<% end_if %>>
    <div class="site-header-inner" <% if $SiteConfig.LogoImage %>style="background-image: url($SiteConfig.LogoImage.Fill(500,686).URL)"<% end_if %>>
        <div class="site-header-inner-text">
            <a class="site-title" href="$BaseHref">
                $SiteConfig.MainText
            </a>
            <p class="site-subtitle">
                $SiteConfig.SubText
            </p>
        </div>
        <div id="open-mobile-menu" class="mobile-menu"><div class="lines"></div></div>
    </div>
    <% include Nav %>
</header>

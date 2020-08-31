<header class="site-header" style="background-image: url($SiteConfig.HeaderImage.URL)">
    <div class="site-header-inner" style="background-image: url($SiteConfig.LogoImage.Fill(500,686).URL)">
        <div class="site-header-inner-text">
            <a class="site-title" href="$BaseHref">
                $SiteConfig.MainText
            </a>
            <p class="site-subtitle">
                $SiteConfig.SubText
            </p>
        </div>
        <div id="open-mobile-menu" class="mobile-menu">
            MENÜ
        </div>
    </div>
    <% include Nav %>
</header>

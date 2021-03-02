<aside class="cta components">
    <% if $SiteConfig.NumBlogArticles > 0 %>
        <h3>Neuigkeiten</h3>
        <% loop $getNewestBlogArticles($SiteConfig.NumBlogArticles) %>
            <% include Streunerkatzen/Includes/BlogArticleListViewSimple %>
        <% end_loop %>
    <% end_if %>

    $SiteConfig.SidebarText
    <% if $SiteConfig.DonateButtonLabel %>
    <button class="components">$SiteConfig.DonateButtonLabel</button>
    <% end_if %>
</aside>

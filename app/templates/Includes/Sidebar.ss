<aside class="cta components">
    <% if $SiteConfig.NumBlogArticles > 0 %>
        <% if $SiteConfig.NumBlogArticles == 1 %>
            <h3>Neuester Blog Eintrag</h3>
        <% else %>
            <h3>Neueste Blog Einträge</h3>
        <% end_if %>
        <% loop $getNewestBlogArticles($SiteConfig.NumBlogArticles) %>
            <% include Streunerkatzen/Includes/BlogArticleListViewSimple %>
        <% end_loop %>
    <% end_if %>

    <button class="components">$SiteConfig.DonateButtonLabel</button>
    $SiteConfig.SidebarText
</aside>

<div class="article-list-container">
    <div class="article-list">
        <h2 class="article-category-header">$Category.Title</h2>
        <% include Streunerkatzen/Blog/Includes/BlogArticleList BlogArticles=$BlogArticles %>
    </div>
    <% if $NumArticlesLeft > 0 %>
    <a class="load-more-btn load-more-articles" data-offset="$Offset" data-url="$Category.Link" href="javascript:void 0">Mehr laden...</a>
    <% end_if %>
</div>

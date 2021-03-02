<div class='article-list'>
    <h2 class='article-category-header'>$Category.Title</h2>
    <% include Streunerkatzen/Blog/Includes/BlogArticleList BlogArticles=$BlogArticles %>
</div>
<% if $NumArticlesLeft > 0 %>
<a class='load-more-btn' id='load-more-btn' data-offset='$Offset' href='javascript:void 0'>Mehr laden...</a>
<% end_if %>

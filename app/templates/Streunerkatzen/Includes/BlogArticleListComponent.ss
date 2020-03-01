<% if $SingleArticle %>
    $SingleArticle.FullView
<% else %>
    <div class='article-list'>
    <% if $FilterCategory %>
        <h2 class='article-category-header'>$FilterCategory</h2>
    <% end_if %>
        <% include Streunerkatzen/BlogArticleList %>
    </div>
    <% if $ArticlesLeft > 0 %>
    <a class='load-more-btn' id='load-more-btn' data-offset='$Offset' href='javascript:void 0'>Mehr laden...</a>
    <% end_if %>
<% end_if %>

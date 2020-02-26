<% if $FilterCategory %>
    <h2 class='article-category-header'>$FilterCategory</h2>
    <% loop $FilteredArticles %>
        $Me.ListView
    <% end_loop %>
<% else_if $SingleArticle %>
    $SingleArticle.FullView
<% else %>
    <% loop $BlogArticleList %>
        $Me.ListView
    <% end_loop %>
<% end_if %>

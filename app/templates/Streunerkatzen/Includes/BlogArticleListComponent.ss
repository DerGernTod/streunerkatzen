<% if not $FilterCategory %>
    <% loop $BlogArticleList %>
        $Me.ListView
    <% end_loop %>
<% else %>
    <h2 class='article-category-header'>$FilterCategory</h2>
    <% loop $FilteredArticles %>
        $Me.ListView
    <% end_loop %>
<% end_if %>

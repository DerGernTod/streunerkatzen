<% if $SingleArticle %>
    $SingleArticle.FullView
<% else %>
    <div class='article-list'>
    <% if $FilterCategory %>
        <h2 class='article-category-header'>$FilterCategory</h2>
    <% end_if %>
        <% loop $BlogArticleList %>
            $Me.ListView
        <% end_loop %>
    </div>
    <% if $HasMore %>
    <div class='button' id='load-more-btn' data-loaded-articles=''>Mehr laden...</div>
    <% end_if %>
<% end_if %>

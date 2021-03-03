<% if $Title && $ShowTitle %><h2 class="element__title">$Title</h2><% end_if %>
<div class="article-list-container">
    <div class="article-list">
        $BlogArticleListView
    </div>
    <% if $DisplayLoadMore %>
    <a class="load-more-btn load-more-articles" data-offset="$NumArticles" data-url="blog/articlesforblogelement/$ID" href="javascript:void 0">Mehr laden...</a>
    <% end_if %>
</div>
<% require javascript("public/javascript/blog.js") %>

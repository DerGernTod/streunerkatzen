<% loop $BlogArticles %>
    <article id="post-$ID">
        <h4>$Title</h4>
        <% if $PostImage %>
            $PostImage.FitMax(800,800)
        <% end_if %>
        <p>$Abstract</p>
        <a class="button" href="$Link">Weiterlesen</a>
    </article>
<% end_loop %>

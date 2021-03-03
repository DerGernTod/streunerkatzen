<% loop $BlogArticles %>
    <article id="post-$ID" class="blog-article">
        <div class="image">
            <% if $PostImage %>
                <img src="$PostImage.FitMax(200,200).URL" />
            <% end_if %>
        </div>
        <div class="text">
            <div>
                <h5>
                    <% loop $Categories %>
                        <% if not First %> / <% end_if %>
                        <a href="$Link">$Title</a>
                    <% end_loop %>
                </h5>
                <h2><a href="$Link">$Title</a></h2>
                <div class="head"> am $PublishTime.Format("dd.MM.yyyy")</div>
            </div>
            <p>$Abstract</p>
            <a class="read-more" href="$Link">Weiterlesen...</a>
        </div>
    </article>
<% end_loop %>

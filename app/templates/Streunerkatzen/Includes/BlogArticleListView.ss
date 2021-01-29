<article id="post-$ID" class="blog-article">
    <div class="image">
        <img src="$PostImage.FitMax(200,200).URL" />
    </div>
    <div class="text">
        <div>
            <h5>
                <% loop $Categories %>
                    <% if not First %> / <% end_if %>
                    <a href="$Up.CategoryLink/$ID">$Title</a>
                <% end_loop %>
            </h5>
            <h2><a href="$Link/$ID">$Title</a></h2>
            <div class="head"> am $PublishTime.Format("dd.MM.yyyy")</div>
        </div>
        <p>$Abstract</p>
        <a class="read-more" href="$Link/$ID">Weiterlesen...</a>
    </div>
</article>

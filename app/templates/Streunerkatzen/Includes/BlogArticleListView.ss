<article id='post-$ID' class='blog-article'>
    <div>
        <h5>
            <% loop $Categories %>
                <% if not First %> / <% end_if %>
                $Title
            <% end_loop %>
        </h5>
        <h2>$Title</h2>
        <div class='head'> am $PublishTime.Format('dd.MM.yyyy')</div>
    </div>

    <div>$Abstract</div>
    <a class="read-more" href="$Link">Weiterlesen...</a>
</article>

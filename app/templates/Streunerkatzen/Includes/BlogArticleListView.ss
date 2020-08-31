<article id='post-$ID' class='blog-article'>
    <div class='text'>
        <div>
            <h5>
                <% loop $Categories %>
                    <% if not First %> / <% end_if %>
                    <a href='$Up.CategoryLink/$ID'>$Title</a>
                <% end_loop %>
            </h5>
            <h2><a href="$Link/$ID">$Title</a></h2>
            <div class='head'> am $PublishTime.Format('dd.MM.yyyy')</div>
        </div>
        <p>$Abstract</p>
        <a class="read-more" href="$Link/$ID">Weiterlesen...</a>
    </div>
    <div class='image'>
        <div >$PostImage.Fill(150,150)</div>
    </div>
</article>

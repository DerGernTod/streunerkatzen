<% if $BlogArticle %>
    <% with $BlogArticle %>
        <article id='post-$ID' class='blog-article single'>
            <div>
                <h5>
                    <% loop $Categories %>
                        <% if not First %> / <% end_if %>
                        <a href='$Link'>$Title</a>
                    <% end_loop %>
                </h5>
                <h2>$Title</h2>
                <div class='head'> am $PublishTime.Format('dd.MM.yyyy')</div>
            </div>
            <div class='captionImage right'>$PostImage.FitMax(800,800)</div>
            <div>$ElementalArea</div>
        </article>
    <% end_with %>
<% else %>
    <p>Dieser Artikel existiert nicht.</p>
<% end_if %>

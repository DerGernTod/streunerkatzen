<div class="cat-shortcode">
    <div><a href="$Link">$Title</a></div>
    <% if $Attachments %>
        <a href="$Attachments.First.AbsoluteURL" target="_blank">$Attachments.First.Fill(100,100)</a>
    <% end_if %>
</div>

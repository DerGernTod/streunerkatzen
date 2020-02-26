<div class="cat-shortcode">
    <% if $Attachments %>
        <a href="$Attachments.First.AbsoluteURL" target="_blank">$Attachments.First.Fill(450,200)</a>
    <% end_if %>
    <div><a href="$Link">Details zu $Title</a></div>
</div>

<div class="cat-summary">
    <div>
        <h3>$LostFoundStatus: <a href="$Link">$Title</a></h3>
        <div><strong>Rasse: </strong>$Breed</div>
        <div><strong>Farbe: </strong><% loop $HairColors %>
            $Title<% if not $Last %>,
            <% end_if %>
        <% end_loop %></div>
    </div>
    <div>
        <% if $Attachments %>
            <a href="$Attachments.First.AbsoluteURL" target="_blank">$Attachments.First.Fill(100,100)</a>
        <% end_if %>
    </div>
</div>

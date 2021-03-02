<% if $Video %>
    <div class="resized type-video" data-width="$Video.Width" data-height="$Video.Height" style="max-width: {$Video.Width}px;">
        $Video
    </div>
<% end_if %>

<% if $Headline %><h2>$Headline</h2><% end_if %>
<% if $Description %><p>$Description</p><% end_if %>
<% if $SlideLink %>
    <div>
        $SlideLink
    </div>
<% end_if %>

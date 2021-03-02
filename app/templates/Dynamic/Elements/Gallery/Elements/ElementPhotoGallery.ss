<% if $Title && $ShowTitle %><h2 class="element__title">$Title</h2><% end_if %>
<% if $Content %>$Content<% end_if %>

<div class="row">
    <% if $Images %>
        <% loop $Images %>
            <% if $Image.URL %>
                <div class="col-md-3 col-sm-4 col-6 photogallery-holder" style="margin-bottom: 30px;">
                    <a href="$Image.URL" data-lightbox="gallery-{$Up.ID}" <% if $Title && $ShowTitle %>data-title="<h4>$Title</h4> $Content"<% else %>data-title="$Content"<% end_if %>>
                        <img src="$Image.Fill(576,576).URL" alt="$Image.Title" class="img-fluid">
                    </a>
                </div>
            <% end_if %>
        <% end_loop %>
    <% end_if %>
</div>

<% require css('dynamic/silverstripe-elemental-gallery: thirdparty/lightbox/lightbox.css') %>

<% require javascript('silverstripe/admin: thirdparty/jquery/jquery.js') %>
<% require javascript('dynamic/silverstripe-elemental-gallery: thirdparty/lightbox/lightbox.min.js') %>

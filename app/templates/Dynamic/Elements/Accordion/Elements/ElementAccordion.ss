<div class="element_content__content <% if $Style %>element_content__$ExtraClass<% end_if %>">
    <% if $Title && $ShowTitle %><h3>$Title</h3><% end_if %>
    <% if $Content %>$Content<% end_if %>
    <% if $Panels %>
        <div id="accordion-{$ID}" class="accordion" role="tablist">
            <% loop $Panels %>
                <h3><% if $ShowTitle %>$Title<% end_if %></h3>
                <div>
                    <% if $Image %>
                        <img src="$Image.URL" class="img-responsive" alt="$Title.ATT">
                    <% end_if %>
                    $Content
                    <% if $ElementLink %>$ElementLink<% end_if %>
                </div>
            <% end_loop %>
        </div>
    <% end_if %>
</div>

<% require css('dynamic/silverstripe-elemental-accordion: thirdparty/jquery-ui/jquery-ui.min.css') %>
<% require javascript('silverstripe/admin: thirdparty/jquery/jquery.js') %>
<% require javascript('dynamic/silverstripe-elemental-accordion: thirdparty/jquery-ui/jquery-ui.min.js') %>
<% require javascript('public/javascript/accordion.init.js') %>

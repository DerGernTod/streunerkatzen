<div class="multi-image-select-container">
    <select $AttributesHTML>
        <% loop $Options %>
            <option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
        <% end_loop %>
    </select>
    <div class="multi-image-select hidden">
        <div class="selected-options">
            <% loop $Options %>
                <div data-value="$Value.XML"><span>$Title.XML</span><span class="close">❌</span></div>
            <% end_loop %>
        </div>
        <div class="options hidden">
            <% loop $Options %>
                <div data-value="$Value.XML" class="option<% if $Selected %> selected<% end_if %>">
                    <div class="option-container">
                        <div class="option-content">
                            <div class="images<% if $Examples.Count == 0 %> none<% end_if %>">
                                <% loop $Examples.Limit(3) %>
                                    <% if $TotalItems == 1 %>
                                        $Me.FillMax(500, 230)
                                    <% end_if %>
                                    <% if $TotalItems == 2 %>
                                        <div class="half">
                                            $Me.FillMax(400, 230)
                                        </div>
                                    <% end_if %>
                                    <% if $TotalItems >= 3 %>
                                        <div class="third">
                                            $Me.FillMax(270, 230)
                                        </div>
                                    <% end_if %>
                                <% end_loop %>
                            </div>
                            <div class="label">
                                <p>$Title.XML</p>
                            </div>
                            <div class="selected-check"></div>
                        </div>
                    </div>
                </div>
            <% end_loop %>
        </div>
    </div>
</div>

<% require javascript('public/javascript/imagelistbox.js') %>

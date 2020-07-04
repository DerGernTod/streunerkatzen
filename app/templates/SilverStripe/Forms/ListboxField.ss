<select $AttributesHTML>
<% loop $OptionsWithExamples %>
	<option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled" <% end_if %>
        <% loop $Examples %>
            data-ex-$Pos="$AbsoluteLink()"
        <% end_loop %>
        >$Title.XML</option>
<% end_loop %>
</select>

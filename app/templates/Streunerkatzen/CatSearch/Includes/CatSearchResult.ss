<div class='search-results'>
    <% if $Results %>
        <ul>
        <% loop $Results %>
            <li>
                <% include Streunerkatzen/CatSummary %>
            </li>
        <% end_loop %>
        </ul>
        <% if $Results.MoreThanOnePage %>
            <div class='pagination'>
                <% if $Results.NotFirstPage %>
                    <a href="$Results.PrevLink">&lt;</a>
                <% end_if %>
                <% loop $Results.PaginationSummary %>
                <% if $CurrentBool %>
                    <span>$PageNum</span>
                <% else %>
                    <a href="$Link">$PageNum</a>
                <% end_if %>
                <% end_loop %>
                <% if $Results.NotLastPage %>
                    <a href="$Results.NextLink">&gt;</a>
                <% end_if %>
            </div>
        <% end_if %>
    <% else %>
        <p>Keine Katzen gefunden </p>
    <% end_if %>
</div>

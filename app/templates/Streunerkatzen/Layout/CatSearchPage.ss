<div>cat search</div>
$CatSearchForm
<ul>
<% loop $Results %>
    <li><a href="$Link">$Title: $Breed</a></li>
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

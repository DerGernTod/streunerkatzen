<h1>Benachrichtigung abbestellen</h1>
<div>
    <% if $MatchFound %>
        <% loop $Agents %>
        <p>Wir werden an $Email keine Suchbenachrichtigungen mehr zu folgender Suche senden: $ReadableSearch</p>
        <% end_loop %>
    <% else %>
    <p>Wir haben keine Benachrichtigungen zum Abbestellen gefunden.</p>
    <% end_if %>
</div>

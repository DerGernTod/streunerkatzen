<h1>Hi!</h1>

<p>
    Du erhältst diese E-Mail, weil du eine oder mehrere Katzen auf <a href="$BaseHref">streunerkatzen.org</a> eingetragen hast. Du kannst weiter unten deine Benachrichtigungseinstellungen für jede Katze einzeln festlegen.
</p>

<% loop $Me %>
    <% with $Cat %>
        <% include Streunerkatzen/Includes/Mail/CatSummaryMail %>
    <% end_with %>

    <div>
        <a href="$ConfigureURL">Benachrichtigungseinstellungen</a>
    </div>

    <hr />
<% end_loop %>

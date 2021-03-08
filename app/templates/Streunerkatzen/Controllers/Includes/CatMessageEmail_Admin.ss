<% with $Cat %>
<p>Zur Katze "$Title" vom $PublishTime.Format('dd.MM.y') hat jemand eine Nachricht hinterlassen:</p>
<% end_with %>
<p>$Message</p>
<hr />
<p>Link zum Eintrag: <a href="$Cat.AbsoluteLink">$Cat.AbsoluteLink</a></p>
<p>Der Ersteller des Eintrags hat keine E-Mail Adresse als Kontaktmöglichkeit hinterlassen. Folgende Kontaktdaten wurden eingetragen:</p>
<p>$Cat.Contact</p>
<p>Bitte leite diese Nachricht an den Ersteller weiter.</p>

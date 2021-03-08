<% with $Cat %>
<p>Zu deinem Eintrag "$Title" vom $PublishTime.Format('dd.MM.y') hat jemand eine Nachricht hinterlassen:</p>
<% end_with %>
<p>$Message</p>
<hr />
<p>Link zu deinem Eintrag: <a href="$Cat.AbsoluteLink">$Cat.AbsoluteLink</a></p>
<p>Solltest du von unserer Seite öfter Spam erhalten, melde dich bitte unter <a href="mailto:$AdminMail">$AdminMail</a> damit wir uns darum kümmern können.</p>

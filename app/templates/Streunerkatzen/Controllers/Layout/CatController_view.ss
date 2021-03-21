<% if $Cat %>
    <% with $Cat %>
        <h1>$Title</h1>
        <div class='cat-meta'>
            Hinzugefügt am $PublishTime.Format("dd.MM.YYYY") um $PublishTime.Format("H:mm")
        </div>
        <div class="cat-about">
            <h2>Details</h2>
            <ul class='cat-details'>
                <li><span>Rasse</span><span>$Normalized('Breed')</span></li>
                <li><span>Alter</span><span>$Normalized('Age')</span></li>
                <li>
                    <span>Geschlecht</span>
                    <span>
                        <% if $Gender == 'männlich' %>
                        ♂
                        <% else_if $Gender == 'weiblich' %>
                        ♀
                        <% else %>
                        ?
                        <% end_if %>
                    </span>
                </li>
                <li><span>Fellfarbe</span><span>$FurColor</span></li>
                <li><span>Haarlänge</span><span>$FurLength</span></li>
                <li><span>Besonderheiten</span><span>$Normalized('Characteristics')</span></li>
                <li><span>Farbliche Besonderheiten</span><span>$Normalized('ColorCharacteristics')</span></li>
                <li><span>Augenfarbe</span><span>$Normalized('EyeColor')</span></li>
                <li><span>Tattoo</span><span>$Normalized('Tattoo')</span></li>
                <li>
                    <span>Halsband?</span>
                    <span>
                        <% if $HasPetCollar %>
                        ✔
                        <% else %>
                        ✗
                        <% end_if %>
                    </span>
                </li>
                <% if $HasPetCollar %>
                <li><span>Beschreibung des Halsbands</span><span>$Normalized('PetCollarDescription')</span></li>
                <% end_if %>
                <li><span>Kastriert?</span><span>$Check('IsCastrated')</span></li>
                <li><span>Hauskatze?</span><span>$Check('IsHouseCat')</span></li>
                <li><span>Gechippt?</span><span>$Check('IsChipped')</span></li>
                <% if $IsChipped == 'ja' %>
                    <li><span>Chipnummer</span><span>$Normalized('ChipNumber')</span></li>
                <% end_if %>
                <li><span>Verhalten gegenüber Besitzer</span><span>$Normalized('BehaviourOwner')</span></li>
                <li><span>Verhalten gegenüber Fremden</span><span>$Normalized('BehaviourStranger')</span></li>
            </ul>
            <h2>$LostFoundStatus</h2>
            <ul class="cat-details">
                <li><span>Datum</span><span>$LostFoundDate</span></li>
                <li><span>Zeitpunkt</span><span>$LostFoundTime</span></li>
                <li><span>Straße</span><span>$Street</span></li>
                <li><span>Ort</span><span>$Town</span></li>
                <li><span>PLZ</span><span>$ZipCode</span></li>
                <li><span>Bundesland</span><span>$Country</span></li>
                <li><span>Beschreibung der Situation</span><span>$LostFoundDescription</span></li>
                <li><span>Details</span><span>$MoreInfo</span></li>
            </ul>
        </div>
        <div class="cat-attachments">
            <h2>Nachricht an Ersteller</h2>
            <p>Sende eine Nachricht an den Ersteller dieses Eintrags. Denke daran, Kontaktdaten anzufügen, sodass dieser dich erreichen kann.</p>
            <div class="form-container">
                $Up.ContactForm($ID)
            </div>
            <h2>Anhänge</h2>
            <ul class="cat-details">
                <% if $Attachments %>
                    <% loop $Attachments %>
                        <li><a href="$Me.AbsoluteLink" target="_blank"><% if $Me.IsImage %>$Me.Fill(300,300)<% else %>$Me.Title<% end_if %></a></li>
                    <% end_loop %>
                <% else %>
                    <li><span>Keine</span></li>
                <% end_if %>

            </ul>
        </div>
    <% end_with %>
<% else %>
    <p>Katze nicht gefunden!</p>
<% end_if %>

<?php 
    add_filter( 'gettext', 'plugin_translation', 20, 3 );
    function plugin_translation( $translated_text, $text, $domain ) {
        //echo "<pre>" . $text . "</pre>";
        if ( $domain == 'casasync') {

            switch ( $text ) {

               


                case 'Buy':  $translated_text = 'Kaufen';break;
                case 'Rent': $translated_text = 'Mieten';break;

                case 'First name':          $translated_text = 'Vorname';break;
                case 'Last name':           $translated_text = 'Nachname';break;
                case 'Email':               $translated_text = 'E-Mail';break;
                case 'Salutation':          $translated_text = 'Anrede';break;
                case 'Title':               $translated_text = 'Titel';break;
                case 'Phone':               $translated_text = 'Telefon';break;
                case 'Company':             $translated_text = 'Firma';break;
                case 'Street':              $translated_text = 'Strasse';break;
                case 'ZIP':                 $translated_text = 'PLZ';break;
                case 'City':                $translated_text = 'Stadt';break;
                //case 'Locality':            $translated_text = 'Ort';break;
                case 'Kanton':              $translated_text = 'Kanton';break;
                case 'Subject':             $translated_text = 'Betreff';break;
                case 'Message':             $translated_text = 'Nachricht';break;
                case 'Recipient':           $translated_text = 'Rezipient';break;
                case 'Required':            $translated_text = 'Erforderlich';break;
                case '&larr; Page back':    $translated_text = '&larr; Seite zurück';break;
                case 'Page forward &rarr;': $translated_text = 'nächste Seite &rarr;';break;
                case 'Please consider the following errors and try sending it again': $translated_text = 'Folgende Fehler sind aufgetreten. Bitte überprüfen Sie die Eingabefelder.';break;

                case 'Switzerland':         $translated_text = 'Schweiz';break;
                case 'Italy':               $translated_text = 'Italien';break;
                case 'France':              $translated_text = 'Frankreich';break;
                case 'monthly':             $translated_text = 'monatlich';break;
                case 'weekly':              $translated_text = 'wöchentlich';break;
                case 'daily':               $translated_text = 'täglich';break;
                case 'yearly':              $translated_text = 'jährlich';break;
                case 'hourly':              $translated_text = 'stündlich';break;
                case 'month':               $translated_text = 'Monat';break;
                case 'week':                $translated_text = 'Woche';break;
                case 'day':                 $translated_text = 'Tag';break;
                case 'year':                $translated_text = 'Jahr';break;
                case 'per month':           $translated_text = 'pro Monat';break;
                case 'per week':            $translated_text = 'pro Woche';break;
                case 'per day':             $translated_text = 'pro Tag';break;
                case 'per year':            $translated_text = 'pro Jahr';break;
                case 'per hour':            $translated_text = 'pro Stunde';break;
                case 'Base data':           $translated_text = 'Grunddaten';break;
                case 'Plans & Documents':   $translated_text = 'Pläne & Dokumente';break;
                case 'Rooms:':              $translated_text = 'Zimmer:';break;
                case 'Rooms':               $translated_text = 'Zimmer';break;
                case 'Floor':               $translated_text = 'Etage';break;
                case 'Rent price:':         $translated_text = 'Mietpreis:';break;
                case 'Object ID':           $translated_text = 'Objekt-ID';break;
                case 'Floor(s)':            $translated_text = 'Stockwerk(e)';break;



                case 'Surroundings': $translated_text = 'Umfeld';break;
                case 'Distances:':   $translated_text = 'Distanzen:';break;
                case 'Plans':        $translated_text = 'Pläne';break;


                case 'Shopping':              $translated_text = 'Einkaufen';break;
                case 'Rail connection':       $translated_text = 'Bahnanschluss';break;

                
                case 'Directly contact the provider now': $translated_text = 'Jetzt Anbieter direkt kontaktieren';break;
                case 'Back to the list':                  $translated_text = 'Zurück zur Übersicht';break;


                case 'Please fill out all the fields': $translated_text = 'Bitte alle Felder ausfüllen.';break;
                case 'Send':                           $translated_text = 'Senden';break;
                case 'Contact directly':               $translated_text = 'Direkt kontaktieren';break;

                case 'Provider':           $translated_text = 'Anbieter';break;
                case 'Seller':             $translated_text = 'Verkäufer';break;

                case 'Choose category': $translated_text = 'Kategorie wählen';break;
                case 'Choose locality': $translated_text = 'Ort wählen';break;
                case 'Choose offer':    $translated_text = 'Angebot wählen';break;

                case 'Advanced search': $translated_text = 'Erweiterte Suche';break;
                case 'Search':          $translated_text = 'Suchen';break;
                case 'Details':         $translated_text = 'Details';break;

                case 'and': $translated_text = 'und';break;




                case 'Wheelchair accessible': $translated_text = 'Rollstuhlzugänglich'; break;
                case 'Entrances': $translated_text = 'Eingänge';break;

                case 'Garage':                  $translated_text = 'Garage';break;
                case '%d balconies':            $translated_text = '%dx Balkone';break;
                case 'ISDN connection':         $translated_text = 'ISDN Anschluss';break;
                case '%d elevators':            $translated_text = '%d Lifte / Aufzüge';break;



                case 'Industrial Objects': $translated_text = 'Gewerbe/Industrie';break;


                case 'Available:':         $translated_text = 'Verfügbar:';break;
                case 'Reserved':           $translated_text = 'Reserviert';break;
                case 'Planned':            $translated_text = 'In Planung';break;
                case 'Under construction': $translated_text = 'Im Bau';break;




                /* New */
                case 'Agricultural installation': $translated_text = 'Landwirtschaftsbetrieb';break;
                case 'Mountain farm':             $translated_text = 'Alpwirtschaft';break;
                case 'Farm':                      $translated_text = 'Farm';break;

                case 'Duplex':         $translated_text = 'Maisonette / Duplex';break;
                case 'Attic flat':     $translated_text = 'Attikawohnung';break;
                case 'Roof flat':      $translated_text = 'Dachwohnung';break;
                case 'Studio':         $translated_text = 'Studio';break;
                case 'Single Room':    $translated_text = 'Einzelzimmer';break;
                case 'Furnished flat': $translated_text = 'Möbl. Wohnobj.';break;
                case 'Terrace flat':   $translated_text = 'Terrassenwohnung';break;
                case 'Bachelor flat':  $translated_text = 'Einliegerwohnung';break;
                case 'Loft':           $translated_text = 'Loft';break;
                case 'Attic':          $translated_text = 'Mansarde';break;

                case 'Alottment garden': $translated_text = 'Schrebergarten';break;

                case 'Hotel':                     $translated_text = 'Hotel';break;
                case 'Restaurant':                $translated_text = 'Restaurant';break;
                case 'Coffeehouse':               $translated_text = 'Café';break;
                case 'Bar':                       $translated_text = 'Bar';break;
                case 'Club / Disco':              $translated_text = 'Club / Disco';break;
                case 'Casino':                    $translated_text = 'Casino';break;
                case 'Movie / theater':           $translated_text = 'Kino / Theater';break;
                case 'Squash / Badminton':        $translated_text = 'Squash / Badminton';break;
                case 'Indoor tennis courts':      $translated_text = 'Tennishalle';break;
                case 'Tennis court':              $translated_text = 'Tennisplatz';break;
                case 'Sports hall':               $translated_text = 'Sportanlage';break;
                case 'Campground / Tent camping': $translated_text = 'Camping- / Zeltplatz';break;
                case 'Outdoor swimming pool':     $translated_text = 'Freibad';break;
                case 'Indoor swimmingpool':       $translated_text = 'Hallenbad';break;
                case 'Golf course':               $translated_text = 'Golfplatz';break;
                case 'Motel':                     $translated_text = 'Motel';break;
                case 'Pub':                       $translated_text = 'Pub';break;

                case 'Single house':               $translated_text = 'Einfamilienhaus';break;
                case 'Row house':                  $translated_text = 'Reihenfamilienhaus';break;
                case 'Bifamiliar house':           $translated_text = 'Doppeleinfamilienhaus';break;
                case 'Terrace house':              $translated_text = 'Terrassenhaus';break;
                case 'Villa':                      $translated_text = 'Villa';break;
                case 'Farm house':                 $translated_text = 'Bauernhaus';break;
                case 'Multiple dwelling':          $translated_text = 'Mehrfamilienhaus';break;
                case 'Cave house / earthen house': $translated_text = 'Höhlen- / Erdhaus';break;
                case 'Castle':                     $translated_text = 'Schloss';break;
                case 'Granny flat':                $translated_text = 'Stöckli';break;
                case 'Chalet':                     $translated_text = 'Chalet';break;
                case 'Rustic house':               $translated_text = 'Rustico';break;

                case 'Office':                       $translated_text = 'Büro';break;
                case 'Shop':                         $translated_text = 'Ladenfläche';break;
                case 'Advertising area':             $translated_text = 'Werbefläche';break;
                case 'Storage room':                 $translated_text = 'Lager';break;
                case 'Practice':                     $translated_text = 'Praxis';break;
                case 'Kiosk':                        $translated_text = 'Kiosk';break;
                case 'Gardening':                    $translated_text = 'Gärtnerei';break;
                case 'Fuel station':                 $translated_text = 'Tankstelle';break;
                case 'Cheese factory':               $translated_text = 'Käserei';break;
                case 'Butcher':                      $translated_text = 'Metzgerei';break;
                case 'Bakery':                       $translated_text = 'Bäckerei';break;
                case 'Hairdresser':                  $translated_text = 'Coiffeursalon';break;
                case 'Factory':                      $translated_text = 'Fabrik';break;
                case 'Industrial object':            $translated_text = 'Industrieobjekt';break;
                case 'Arcade':                       $translated_text = 'Arcade';break;
                case 'Atelier':                      $translated_text = 'Atelier';break;
                case 'Living / commercial building': $translated_text = 'Wohn- / Geschäftshaus';break;
                case 'Library':                      $translated_text = 'Bücherei';break;
                case 'Hospital':                     $translated_text = 'Krankenhaus';break;
                case 'Laboratory':                   $translated_text = 'Labor';break;
                case 'Mini-golf course':             $translated_text = 'Minigolfplatz';break;
                case 'nursing home':                 $translated_text = 'Pflegeheim';break;
                case 'Riding hall':                  $translated_text = 'Reithalle';break;
                case 'Sanatorium':                   $translated_text = 'Sanatorium';break;
                case 'Workshop':                     $translated_text = 'Werkstatt';break;
                case 'Party room':                   $translated_text = 'Partyraum';break;
                case 'Sauna':                        $translated_text = 'Sauna';break;
                case 'Solarium':                     $translated_text = 'Solarium';break;
                case 'Carpentry shop':               $translated_text = 'Schreinerei';break;
                case 'Old-age home':                 $translated_text = 'Altersheim';break;
                case 'Department store':             $translated_text = 'Geschäftshaus';break;
                case 'Home':                         $translated_text = 'Heim';break;
                case 'Display window':               $translated_text = 'Schaufenster';break;
                case 'Parking garage':               $translated_text = 'Parkhaus';break;
                case 'Parking surface':              $translated_text = 'Parkfläche';break;

                case 'Open slot':                  $translated_text = 'offener Parkplatz';break;
                case 'Covered slot':               $translated_text = 'Unterstand';break;
                case 'Single garage':              $translated_text = 'Einzelgarage';break;
                case 'Double garage':              $translated_text = 'Doppelgarage';break;
                case 'Underground slot':           $translated_text = 'Tiefgarage';break;
                case 'Boat dry dock':              $translated_text = 'Boot Hallenplatz';break;
                case 'Boat landing stage':         $translated_text = 'Boot Stegplatz';break;
                case 'Covered parking place bike': $translated_text = 'Moto Hallenplatz';break;
                case 'Outdoor parking place bike': $translated_text = 'Moto Aussenplatz';break;
                case 'Horse box':                  $translated_text = 'Stallboxe';break;
                case 'Boat mooring':               $translated_text = 'Boot Bojenplatz';break;

                case 'Building land':     $translated_text = 'Bauland';break;
                case 'Agricultural land': $translated_text = 'Agrarland';break;
                case 'Commercial land':   $translated_text = 'Gewerbeland';break;
                case 'Industrial land':   $translated_text = 'Industriebauland';break;

                case 'Hobby room':         $translated_text = 'Hobbyraum';break;
                case 'Cellar compartment': $translated_text = 'Kellerabteil';break;
                case 'Attic compartment':  $translated_text = 'Estrichabteil';break;

                case 'Floor':                       $translated_text = 'Etage';break;

                case 'available':           $translated_text = 'Verfügbar';break;
                case 'reserved':            $translated_text = 'Reserved';break;
                case 'planned':             $translated_text = 'In Planung';break;
                case 'under-construction':  $translated_text = 'Im Bau';break;
                case 'reference':           $translated_text = 'Referenz';break;
                case 'No image':            $translated_text = 'Kein Bild';break;
                case 'No results':          $translated_text = 'Keine Suchergebnisse';break;
                case '(net)':               $translated_text = '(Netto)';break;
                case '(gross)':             $translated_text = '(Brutto)';break;


                case 'Nothing Found': $translated_text = 'Nichts gefunden';break;
                case 'Sorry, but nothing matched your search terms. Please try again with some different keywords.': $translated_text = 'Es tut uns leid, aber auf ihre Suchanfrage gab es keine Treffer. Bitte versuchen Sie es mit anderen Suchbegriffen.';break;


                case 'Ascension': $translated_text = 'Ascension';break;
                case 'United Arab Emirates': $translated_text = 'Vereinigte Arabische Emirate';break;
                case 'Åland Islands': $translated_text = 'Aland';break;
                case 'Bolivia, Plurinational State of': $translated_text = 'Bolivien';break;
                case 'Libya': $translated_text = 'Libyen';break;
                case 'Montenegro': $translated_text = 'Montenegro';break;
                case 'Macedonia, the former Yugoslav Republic of': $translated_text = 'Mazedonien';break;
                case 'Palestinian Territory, Occupied': $translated_text = 'Palästinensische Autonomiegebiete';break;
                case 'Saint Helena, Ascension and Tristan da Cunha': $translated_text = 'Die Kronkolonie St. Helena und Nebengebiete';break;
                case 'Samoa': $translated_text = 'Samoa';break;

            }

        }

        return $translated_text;
    }
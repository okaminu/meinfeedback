<?php
namespace MFB\ServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\RatingBundle\Entity\Rating;
use MFB\ServiceBundle\Entity\Business;
use MFB\ServiceBundle\Entity\ServiceDefinition;
use MFB\ServiceBundle\Entity\ServiceType;
use MFB\ServiceBundle\Entity\ServiceTypeCriteria;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MFB\ServiceBundle\Entity\ServiceTypeDefinition;

class LoadServiceData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $businessList = array(
        array(
            'name' => 'Beratungs-Dienstleistungen',
            'multiple' => 1,
            'service_types' =>
                array('Rechtsanwälte & Notare', 'Arbeitsvermittlungen & Berufsberatung', 'Beratende Ingenieure',
                    'Architekten & Innenarchitekten', 'Steuerberater & Buchhalter', 'Grafikdesign',
                    'Coaching & Lebensberatung', 'Sicherheit & Privatdetektive', 'Webdesign & Webprogrammierung',
                    'Werbung & Marketing', 'Öffentlichkeitsarbeit & PR', 'Übersetzungen & Dolmetscher',
                    'Immobilienmakler', 'Immobiliensachverständige')
        ),
        array(
            'name' => 'Handel',
            'multiple' => 1,
            'service_types' =>
                array('Autohändler', 'Elektronik', 'Lebensmittel', 'Baumarkt', 'Gartenbaumarkt', 'Möbel', 'Schmuck',
                    'Mode, Bekleidung', 'Fahrrad', 'Apotheken', 'Augenoptiker', 'Hörgeräteakustiker')
        ),
        array(
            'name' => 'Handwerkliche Dienstleistungen',
            'multiple' => 1,
            'service_types' =>
                array('Autowerkstätten & Autopflege', 'Bauunternehmen', 'Dachdecker', 'Elektriker', 'Fensterbauer',
                    'Fußbodenbeläge', 'Gärtner & Baumpflege', 'Allround-Handwerker', 'Heizung & Klimaanlagen',
                    'Klempner, Sanitär', 'Landschaftsbau', 'Maler & Tapezierer', 'Mauerwerks- und Betonbau',
                    'Reinigungskräfte', 'Schlüsseldienste', 'Schreiner, Tischler, Möbelbau', 'Sicherheitssysteme',
                    'Solarinstallationen', 'Raumausstatter', 'Umzugsunternehmen')
        ),
        array(
            'name' => 'Heil- & Pflegeberufe',
            'multiple' => 1,
            'service_types' =>
                array('Ärzte', 'Zahnärzte', 'Heilpraktiker', 'Psychotherapeuten', 'Tierärzte', 'TCM & Akupunktur',
                    'Chiropraktiker & Osteopathen', 'Ernährungsberater & Diätzentren', 'Gesundheitszentren & Kliniken',
                    'Hebammen', 'Logopäden', 'Medizinische Fusspflege', 'Pflegedienste & Pflegeheim',
                    'Physiotherapie & Massage', 'Professionelle Zahnreinigung', 'Beauty & Kosmetik')
        ),
        array(
            'name' => 'Hersteller & Produzenten',
            'multiple' => 1,
            'service_types' =>
                array()
        ),
        array(
            'name' => 'Restaurants & Hotels',
            'multiple' => 1,
            'service_types' =>
                array('Restaurants', 'Bars & Cafes', 'Pensionen, B&B', '1 * Hotels', '2 * Hotels', '3 * Hotels',
                    '4 * Hotels', '5 * Hotels')
        )
    );

    private $serviceTypeCriteriasDefinitions = array(
        array(
            'name' => 'Rechtsanwälte & Notare',
            'definition' => array('Agrarrecht', 'Arbeitsrecht', 'Bank- und Kapitalmarktrecht',
                'Bau- und Architektenrecht', 'Erbrecht', 'Familienrecht', 'Gewerblicher Rechtsschutz',
                'Handels- und Gesellschaftsrecht', 'Informationstechnologierecht', 'Insolvenzrecht', 'Medizinrecht',
                'Miet- und Wohnungseigentumsrecht', 'Sozialrecht', 'Steuerrecht', 'Strafrecht',
                'Transport- und Speditionsrecht', 'Urheber- und Medienrecht', 'Verkehrsrecht', 'Versicherungsrecht',
                'Verwaltungsrecht', 'Patentrecht', 'Notariat'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Arbeitsvermittlungen & Berufsberatung',
            'definition' => array('Arbeitsvermittlung', 'Berufsberatung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Beratende Ingenieure',
            'definition' => array('Ingenieurbau', 'Verkehr', 'Wasser', 'Energie', 'Hoch- und Tiefbau', 'Umwelt'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Architekten & Innenarchitekten',
            'definition' => array('Architektur', 'Innenarchitektur', 'Landschaftsplanung', 'Städtebau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Steuerberater & Buchhalter',
            'definition' => array('Steuerberatung', 'Lohnbuchhaltung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Grafikdesign',
            'definition' => array('Logo-Entwicklung', 'Briefpapier', 'Corporate Design'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Coaching & Lebensberatung',
            'definition' => array('Kommunikation', 'NLP', 'Hypnosetherapie'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Sicherheit & Privatdetektive',
            'definition' => array('Objektschutz', 'Personenschutz'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Webdesign & Webprogrammierung',
            'definition' => array('Datenbanken', 'Mobile Webseiten', 'Apps', 'Grafikdesign'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Werbung & Marketing',
            'definition' => array('Grafikdesign', 'Werbetexte'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Öffentlichkeitsarbeit & PR',
            'definition' => array('Werbetexte', 'Öffentlichkeitsarbeit', 'Produkt-PR', 'Firmenkommunikation'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Übersetzungen & Dolmetscher',
            'definition' => array('Deutsch-Englisch', 'Englisch-Deutsch', 'Deutsch-Französisch', 'Französisch-Deutsch'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Immobilienmakler',
            'definition' => array('Mietobjekte', 'Kaufobjekte', 'Suche für Kunden'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Immobiliensachverständige',
            'definition' => array('Grundstücksbewertungen', 'Immobilienbewertungen', 'Wohnungen'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Autohändler',
            'definition' => array('Alle Marken', 'Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley', 'BMW', 'Chevrolet',
                'Chrysler', 'Citroën', 'Dacia', 'Daihatsu', 'Fiat', 'Ford', 'Honda', 'Hyundai', 'Infiniti', 'Jaguar',
                'Kia', 'Lada', 'Lancia', 'Land Rover', 'Lexus', 'Lotus', 'Maserati', 'Mazda', 'Mercedes Benz', 'Mini',
                'Mitsubishi', 'Nissan', 'Opel', 'Peugeot', 'Porsche', 'Renault', 'Seat', 'Skoda', 'Smart', 'Subaru',
                'Suzuki', 'Toyota', 'Volkswagen', 'Volvo'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Elektronik',
            'definition' => array('TV/Heimkino', 'Haushaltselektronik',	'Hifi'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Lebensmittel',
            'definition' => array('Feinkost', 'Getränke'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Baumarkt',
            'definition' => array('Werkzeug', 'Elektrogeräte', 'Gartenartikel', 'Lagerartikel'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Gartenbaumarkt',
            'definition' => array('Bäume', 'Hecken & Sträucher', 'Saat', 'Gartenmöbel'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Möbel',
            'definition' => array('Gartenmöbel', 'Küchen', 'Wohnzimmer', 'Schlafzimmer', 'Kinderzimmer', 'Badezimmer'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Schmuck',
            'definition' => array('Damen-Uhren', 'Herren-Uhren', 'Damen-Schmuck', 'Herren-Schmuck'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Mode, Bekleidung',
            'definition' => array('Damen-Bekleidung', 'Herren-Bekleidung'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Fahrrad',
            'definition' => array('Fahrräder', 'Fahrrad-Zubehör'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Apotheken',
            'definition' => array('Verschreibungspflichtige Medikamente', 'Verschreibungsfreie Medikamente', 'Kosmetik'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Augenoptiker',
            'definition' => array('Beratung', 'Sonnenbrillen', 'Kontaktlinsen'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Hörgeräteakustiker',
            'definition' => array('Beratung'),
            'criterias' => array('Preis/Leistung', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Autowerkstätten & Autopflege',
            'definition' => array('Ölwechsel', 'Reifenwechsel', 'Reifeneinlagerung', 'Fehlerdiagnose', 'Wintercheck',
                'Klimaanlagen-Desinfektion', 'Klimaservice', 'HU/AU', 'Außenreinigung', 'Innenreinigung', 'Alle Marken',
                'Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley', 'BMW', 'Chevrolet', 'Chrysler', 'Citroën', 'Dacia',
                'Daihatsu', 'Fiat', 'Ford', 'Honda', 'Hyundai', 'Infiniti', 'Jaguar', 'Kia', 'Lada', 'Lancia',
                'Land Rover', 'Lexus', 'Lotus', 'Maserati', 'Mazda', 'Mercedes Benz', 'Mini', 'Mitsubishi', 'Nissan',
                'Opel', 'Peugeot', 'Porsche', 'Renault', 'Seat', 'Skoda', 'Smart', 'Subaru', 'Suzuki', 'Toyota',
                'Volkswagen', 'Volvo'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Bauunternehmen',
            'definition' => array('Trockenbau', 'Innenausbau', 'Rohbau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Dachdecker',
            'definition' => array('Abdichtung', 'Dachausbau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Elektriker',
            'definition' => array('Elektroplanung', 'Ausführung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Fensterbauer',
            'definition' => array('Holzfenster', 'Metallfenster', 'Wintergartenbau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Fußbodenbeläge',
            'definition' => array('Teppichverlegen', 'Fliesen'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Gärtner & Baumpflege',
            'definition' => array('Gartenbau', 'Baumpflege'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Allround-Handwerker',
            'definition' => array('Malerarbeiten', 'Möbelaufbau', 'Verputzen', 'Verfugen'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Heizung & Klimaanlagen',
            'definition' => array('Heizung', 'Klima'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Klempner, Sanitär',
            'definition' => array('Abwasser', 'Bad/WC'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Landschaftsbau',
            'definition' => array('Garten', 'Bäume', 'Hecken/Sträucher'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Maler & Tapezierer',
            'definition' => array('Malerarbeiten', 'Tapezieren'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Mauerwerks- und Betonbau',
            'definition' => array('Maurerarbeiten', 'Betonbau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Reinigungskräfte',
            'definition' => array('Fußbodenreinigung', 'Büroreinigung', 'Fensterreinigung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Schlüsseldienste',
            'definition' => array('Notdienst', 'Sicherheitsberatung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Schreiner, Tischler, Möbelbau',
            'definition' => array('Fensterbau', 'Möbelbau', 'Küchenbau', 'Innenausbau'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Sicherheitssysteme',
            'definition' => array('Sicherheitsberatung', 'Planung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Solarinstallationen',
            'definition' => array('Planung', 'Fördermittel', 'Installation'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Raumausstatter',
            'definition' => array('Teppichverlegen', 'Tapezieren'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Ärzte',
            'definition' => array('Innere Medizin', 'Allgemeinmedizin', 'Chirurgie', 'Anästhesiologie',
                'Frauenheilkunde und Geburtshilfe', 'Kinder- und Jugendmedizin', 'Orthopädie',
                'Psychiatrie und Psychotherapie', 'Radiologie', 'Augenheilkunde', 'HNO',
                'Haut- und Geschlechtskrankheiten', 'Urologie', 'Arbeitsmedizin', 'Neurologie', 'Kardiologie',
                'Plastische Chirurgie', 'Tropeninstitut'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Zahnärzte',
            'definition' => array('Prophylaxe'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Heilpraktiker',
            'definition' => array('Homöopathie'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Psychotherapeuten',
            'definition' => array('Psychotherapie', 'Gesprächstherapie', 'Hypnosetherapie'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Tierärzte',
            'definition' => array('Hunde', 'Katzen', 'Kleintiere', 'Pferde'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'TCM & Akupunktur',
            'definition' => array('Akupunktur', 'TCM'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Chiropraktiker & Osteopathen',
            'definition' => array('Chiropraktik', 'Osteopathie'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Ernährungsberater & Diätzentren',
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Gesundheitszentren & Kliniken',
            'definition' => array('Ambulanz', 'Stationärer Aufenthalt'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Hebammen',
            'definition' => array('Hausgeburten', 'Beratung', 'Nachbetreuung'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Logopäden',
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Medizinische Fusspflege',
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Pflegedienste & Pflegeheim',
            'definition' => array('mobile Pflege', 'stationäre Pflege'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Physiotherapie & Massage',
            'definition' => array('Massage', 'Physiotherapie'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Professionelle Zahnreinigung',
            'definition' => array('Prophylaxe', 'Bleaching'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Beauty & Kosmetik',
            'definition' => array('Maniküre', 'Pediküre', 'Gesichtsbehandlung', 'Haarentfernung mit IPL',
                'Haarentfernung mit SHR Laser', 'Haarentfernung mit Nadelepilation und Elektroepilation',
                'Endermologie gegen Cellulite und Orangenhaut', 'Hyaluronsäure gegen Falten',
                'Anti Aging und Hautstraffung', 'Fruchtsäurepeeling', 'Permanent Make-Up', 'Akne Behandlung',
                'Aknenarben Behandlung', 'Altersflecken & Pigmentstörungen', 'Besenreiser und Couperose',
                'Mikrodermabrasion'),
            'criterias' => array('Zuverlässigkeit', 'Preis/Leistung', 'Pünktlichkeit/Schnelligkeit', 'Qualität',
                'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Restaurants',
            'definition' => array('Italienisch', 'Französisch', 'Griechisch', 'Deutsche Küche', 'Türkisch', 'Arabisch',
                'Spanisch', 'Mediterran', 'Osteuropäisch', 'Thai', 'Chinesisch', 'Vietnamesisch',
                'Vegetarische Gerichte', 'Vegane Gerichte', 'Vorspeisen', 'Hauptspeisen', 'Nachspeisen',
                'Fischgerichte', 'Fleischgerichte', 'Suppen'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz', 'Auswahl/Sortiment')
        ),
        array(
            'name' => 'Bars & Cafes',
            'definition' => array('Frühstück', 'Cocktails', 'Tagesbar', 'Snacks'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => 'Pensionen, B&B',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => '1 * Hotels',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => '2 * Hotels',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => '3 * Hotels',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => '4 * Hotels',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit',
                'Zimmerservice', 'Zusatzangebot', 'Sauna'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
        array(
            'name' => '5 * Hotels',
            'definition' => array('Zimmerqualität', 'Schall-Isolierung', 'Service', 'Frühstück', 'Freundlichkeit',
                'Zimmerservice', 'Zusatzangebot', 'Sauna'),
            'criterias' => array('Preis/Leistung', 'Qualität', 'Freundlichkeit', 'Kompetenz')
        ),
    );

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadServiceBusiness($manager);
        $this->loadServiceTypeCriteriasDefinitions($manager);
    }

    private function loadServiceBusiness(ObjectManager $manager)
    {
        foreach ($this->businessList as $business) {
            $businessEntity = $this->createNewBusinessEntity($business['name'], $business['multiple']);
            $manager->persist($businessEntity);
            $this->loadServiceTypesForBusiness($manager, $business['service_types'], $businessEntity);
        }
        $manager->flush();
    }

    private function loadServiceTypeCriteriasDefinitions(ObjectManager $manager)
    {
        foreach ($this->serviceTypeCriteriasDefinitions as $type) {
            $serviceType = $manager->getRepository('MFBServiceBundle:ServiceType')->findOneBy(
                array('name' => $type['name'])
            );
            $this->linkCriteriasToServiceType($manager, $type, $serviceType);
            $this->linkDefinitionsToServiceType($manager, $type, $serviceType);
        }
        $manager->flush();
    }

    private function loadServiceTypesForBusiness(ObjectManager $manager, $serviceTypes, $businessEntity)
    {
        foreach ($serviceTypes as $type) {
            $typeEntity = $this->createNewServiceTypeEntity($type, $businessEntity);
            $manager->persist($typeEntity);
        }
        $manager->flush();
    }

    private function linkCriteriasToServiceType(ObjectManager $manager, $type, $serviceType)
    {
        foreach ($type['criterias'] as $criteria) {
            try {
                $rating = $this->getReference("rating-{$criteria}");
            } catch (\Exception $ex) {
                $rating = $this->createNewRatingEntity($criteria);
                $manager->persist($rating);
                $this->addReference("rating-{$criteria}", $rating);
            }
            $serviceCriteria = $this->createServiceCriteriaEntity($serviceType, $rating);
            $manager->persist($serviceCriteria);
        }
        $manager->flush();
    }

    private function linkDefinitionsToServiceType(ObjectManager $manager, $type, $serviceType)
    {
        if (isset($type['definition'])) {
            foreach ($type['definition'] as $definitionName) {

                $definition = $this->getDefinition($manager, $definitionName);
                if ($definition == null) {
                    $definition = $this->createNewDefinitionEntity($definitionName);
                    $manager->persist($definition);
                }

                $service = $this->createServiceTypeDefinitionEntity($serviceType, $definition);
                $manager->persist($service);
            }

            $manager->flush();
        }
    }

    private function createNewBusinessEntity($name, $multiple)
    {
        $entity = new Business();
        $entity->setName($name);
        $entity->setIsMultipleServices($multiple);
        $entity->setIsCustom(false);
        return $entity;
    }

    private function createNewServiceTypeEntity($name, $business)
    {
        $entity = new ServiceType();
        $entity->setBusiness($business);
        $entity->setName($name);
        $entity->setIsCustom(false);
        return $entity;
    }

    private function createNewRatingEntity($criteria)
    {
        $rating = new Rating();
        $rating->setName($criteria);
        return $rating;
    }

    private function createNewDefinitionEntity($definition)
    {
        $definitionEntity = new ServiceDefinition();
        $definitionEntity->setName($definition);
        $definitionEntity->setIsCustom(false);
        return $definitionEntity;
    }

    private function createServiceCriteriaEntity($serviceType, $rating)
    {
        $serviceCriteria = new ServiceTypeCriteria();
        $serviceCriteria->setServiceType($serviceType);
        $serviceCriteria->setRating($rating);
        return $serviceCriteria;
    }

    private function createServiceTypeDefinitionEntity($serviceType, $defitinion)
    {
        $service = new ServiceTypeDefinition();
        $service->setServiceType($serviceType);
        $service->setServiceDefinition($defitinion);
        return $service;
    }

    private function getDefinition($manager, $defName)
    {
         return $manager->getRepository("MFBServiceBundle:ServiceDefinition")->findOneBy(
             array('name' => $defName)
         );
    }


}
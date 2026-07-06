# Założenia projektowe — Bear Marketplace

## 1. Cel projektu

Bear Marketplace to prosta aplikacja typu marketplace (tablica ogłoszeń),
umożliwiająca publikowanie ogłoszeń zarówno przez **firmy**, jak i **osoby
prywatne**. Aplikacja pełni rolę pośrednika prezentującego oferty — nie
obsługuje płatności ani realizacji transakcji. Kontakt między zainteresowanymi
stronami odbywa się bezpośrednio (dane kontaktowe podane w ogłoszeniu lub
wewnętrzny formularz/wiadomości).

Zakres pierwszej wersji (MVP) celowo pozostaje wąski: rejestracja użytkownika,
wystawianie i przeglądanie ogłoszeń oraz podstawowe wyszukiwanie.

> **Stan realizacji:** trzon serwisu (konta, ogłoszenia, przeglądanie,
> wyszukiwanie, profile sprzedających) oraz moderacja (zgłoszenia, panel
> administratora, blokowanie kont) są zaimplementowane. Do zrobienia zostaje
> głównie kontakt między stronami — pełne zestawienie w sekcji 7.

## 2. Odbiorcy i role

| Rola | Opis | Uprawnienia | Stan |
| --- | --- | --- | --- |
| **Gość** | Niezalogowany odwiedzający | Przeglądanie i wyszukiwanie ogłoszeń, podgląd szczegółów, profile sprzedających | ✅ |
| **Osoba prywatna** | Zarejestrowany użytkownik indywidualny | Wystawianie, edycja i usuwanie własnych ogłoszeń | ✅ |
| **Firma** | Zarejestrowane konto firmowe (z danymi firmy, np. NIP) | Jak osoba prywatna + oznaczenie ogłoszeń jako firmowe | ✅ |
| **Administrator** | Obsługa serwisu | Moderacja: ukrywanie/usuwanie ogłoszeń, blokowanie kont; zarządzanie kategoriami 🔲 | 🟡 |

Rozróżnienie **osoba prywatna / firma** to atrybut konta (typ konta), a nie
osobny model użytkownika. Firma podaje dodatkowe dane (nazwa firmy, NIP,
opcjonalnie adres), które są prezentowane przy jej ogłoszeniach.

Rola **administratora** to flaga konta (`is_admin`), a nie osobny typ konta —
niezależna od podziału prywatna/firma. Administrator ma dostęp do panelu
moderacji (`/admin`); zarządzanie słownikiem kategorii z panelu jest jedynym
elementem tej roli, którego jeszcze nie zaimplementowano.

## 3. Główne funkcjonalności (MVP)

Legenda stanu: ✅ zrealizowane · 🟡 częściowo · 🔲 niezrealizowane.

### Konta i uwierzytelnianie
- ✅ Rejestracja i logowanie (e-mail + hasło), sesyjne.
- ✅ Wybór typu konta przy rejestracji: **osoba prywatna** lub **firma**
  (firma zakłada profil firmy z wymaganym NIP-em).
- ✅ Publiczny profil sprzedającego z jego aktywnymi ogłoszeniami
  (`/sprzedawca/{id}`).
- ✅ Panel użytkownika (dashboard) z przeglądem, listą ogłoszeń i danymi konta,
  adaptacyjny dla osoby prywatnej i firmy.
- 🔲 Reset hasła i weryfikacja adresu e-mail.

### Ogłoszenia
- ✅ Utworzenie ogłoszenia: tytuł, opis, cena (opcjonalna / „do negocjacji"),
  kategoria (liść drzewa), lokalizacja, zdjęcia.
- ✅ Edycja i usuwanie własnych ogłoszeń (autoryzacja właściciela przez policy;
  usunięcie kasuje też pliki zdjęć).
- ✅ Statusy ogłoszenia: `robocze` (draft), `aktywne`, `zakończone` oraz
  przejścia między nimi (publikacja, zakończenie, wznowienie). Status `ukryte`
  jest zdefiniowany, ale będzie używany dopiero przez moderację.
- ✅ Automatyczne oznaczenie, czy ogłoszenie pochodzi od firmy, czy osoby
  prywatnej (dziedziczone z typu konta).
- 🔲 Dane kontaktowe w ogłoszeniu (dziś kontakt do sprzedającego nie jest
  zbierany ani prezentowany).

### Przeglądanie i wyszukiwanie
- ✅ Lista ogłoszeń z paginacją, sortowanie (najnowsze, cena rosnąco/malejąco).
- ✅ Filtrowanie po kategorii (z uwzględnieniem poddrzewa), zakresie ceny oraz
  typie wystawiającego (firma / osoba prywatna).
- 🟡 Wyszukiwanie po tytule i opisie — realizowane zapytaniem `LIKE`; pełne
  indeksowanie pełnotekstowe pozostaje usprawnieniem na przyszłość.
- ✅ Widok szczegółów ogłoszenia z galerią zdjęć i danymi wystawiającego,
  z odnośnikiem do publicznego profilu sprzedającego.
- ✅ Strona główna z kategoriami (liczność poddrzewa), najnowszymi ogłoszeniami
  i statystykami.

### Moderacja (administrator)
- ✅ Zgłaszanie przez użytkowników — zarówno ogłoszeń, jak i kont — z wyborem
  powodu i opisem (`/zglos`); zgłoszenia trafiają do kolejki moderacji.
- ✅ Panel administratora (`/admin`, dostęp za flagą `is_admin`): przegląd,
  kolejka zgłoszeń (rozpatrz/odrzuć), moderacja ogłoszeń (ukryj/przywróć/usuń)
  oraz kont (zablokuj/odblokuj). Zablokowane konto nie loguje się, a jego
  ogłoszenia znikają z części publicznej.
- 🔲 Zarządzanie słownikiem kategorii (dziś kategorie powstają wyłącznie przez
  seeder deweloperski).

## 4. Model domenowy

Encje są zaimplementowane w `app/Models/`; statusy i typy to backed enumy PHP
w `app/Enums/` (`AccountType`, `ListingStatus`, `ReportStatus`).

- **User** — konto użytkownika. Pole `account_type` (`private` | `company`)
  określa typ konta. Dane firmowe **nie** są trzymane w tej tabeli.
- **CompanyProfile (Profil firmy)** — dane firmowe wydzielone do osobnej tabeli
  w relacji 1—1 z `User` (istnieje tylko dla kont typu `company`). Pola:
  nazwa firmy, NIP, opcjonalnie adres. NIP jest wymagany przy rejestracji firmy,
  lecz przechowywany bez walidacji w rejestrze (patrz sekcja 7).
- **Listing (Ogłoszenie)** — należy do `User`, do jednej `Category`. Pola:
  tytuł, opis, cena, waluta, lokalizacja, status, znaczniki czasu.
- **Category (Kategoria)** — słownik kategorii **hierarchiczny**: kategoria może
  mieć kategorię nadrzędną (`parent_id`), tworząc drzewo nadrzędna → podkategorie.
  Ogłoszenia przypisywane są zwykle do kategorii liścia.
- **ListingImage (Zdjęcie ogłoszenia)** — należy do `Listing`; przechowuje
  ścieżkę pliku i kolejność.
- **Report (Zgłoszenie)** — zgłoszenie do moderacji. Relacja **polimorficzna**
  (`reportable`): przedmiotem zgłoszenia może być `Listing` **lub** `User`.
  Wiąże też zgłaszającego (`User`), powód (enum `ReportReason`) i status.

Relacje:
- `User 1—1 CompanyProfile` (opcjonalna, tylko dla kont firmowych)
- `User 1—N Listing`
- `Category 1—N Category` (self-relacja: nadrzędna → podkategorie)
- `Category 1—N Listing`
- `Listing 1—N ListingImage`
- `Listing / User 1—N Report` (polimorficznie, przez `reportable`)

## 5. Reguły biznesowe

- ✅ Ogłoszenie może wystawić wyłącznie zalogowany użytkownik.
- ✅ Użytkownik edytuje/usuwa tylko własne ogłoszenia (poza administratorem —
  gdy ten powstanie).
- ✅ Na liście publicznej i profilu sprzedającego widoczne są tylko ogłoszenia
  o statusie `aktywne`; strona szczegółów jest dostępna publicznie tylko dla
  ogłoszeń aktywnych.
- ✅ Typ wystawiającego (firma / osoba prywatna) jest dziedziczony z konta i
  widoczny przy ogłoszeniu — służy też jako filtr wyszukiwania.
- ✅ Konto firmowe wymaga utworzenia profilu firmy (`CompanyProfile`) z nazwą
  firmy oraz NIP-em. NIP jest wymagany, ale przyjmowany bez weryfikacji w
  rejestrze (patrz sekcja 7).
- ✅ Każdy zalogowany użytkownik może zgłosić ogłoszenie lub konto do moderacji
  (nie może zgłosić własnego); powtórne zgłoszenie tego samego wpisu jest
  scalane.
- ✅ Zablokowane konto (`blocked_at`) nie może się zalogować, a jego ogłoszenia
  są wykluczone z części publicznej (`Listing::published()`), profilu
  sprzedającego i strony szczegółów.
- ✅ Dostęp do panelu moderacji (`/admin`) mają wyłącznie konta z flagą
  `is_admin`; administrator nie może zablokować siebie ani innego administratora.

## 6. Założenia techniczne

- **Backend:** Laravel 13 (PHP 8.3). Kontrolery zwracają odpowiedzi Inertia
  (`Inertia::render`) zamiast widoków Blade — Laravel pełni rolę warstwy
  aplikacyjnej i API dla frontendu. Kontrolery działają jak view-model:
  kształtują dane z bazy w dokładne propsy stron.
- **Frontend:** **Vue 3** jako warstwa widoku, spięta z backendem przez
  **Inertia.js** (model „monolit SPA" — brak osobnego REST API dla własnego
  frontendu, dane przekazywane jako propsy stron Inertia).
- **SSR (Server-Side Rendering):** włączony renderer SSR Inertia/Vue dla lepszego
  SEO ogłoszeń i szybszego pierwszego renderu. Wymaga uruchomienia procesu
  serwera SSR (`php artisan inertia:start-ssr`) obok aplikacji.
- **Build / assety:** **Vite** buduje dwa wejścia — kliencki bundle aplikacji
  oraz bundle SSR. Stylowanie: Tailwind CSS v4 (plugin `@tailwindcss/vite`)
  z własnym systemem tokenów motywu (jasny/ciemny) i biblioteką komponentów.
- **Baza danych:** MariaDB (sesje, cache i kolejka na sterowniku `database`).
- **Pliki (zdjęcia):** dysk lokalny w MVP (`storage/`, dysk `public`), docelowo
  możliwy S3.
- **Testy:** PHPUnit (suity `Unit` i `Feature`); testy Feature weryfikują
  odpowiedzi Inertia (komponent + propsy).
- **Uwierzytelnianie:** standardowy mechanizm sesyjny Laravel (Inertia korzysta
  z sesji i ciasteczek, bez tokenów), zbudowany na własnym systemie designu
  (bez Breeze).

> Szczegóły środowiska deweloperskiego, poleceń i architektury frontendu
> znajdują się w `CLAUDE.md` w katalogu głównym projektu.

## 7. Funkcjonalności niezaimplementowane (backlog)

Poniżej zebrano to, czego jeszcze nie ma, w podziale na trzy poziomy priorytetu.

### Konieczne
Domykają zakres MVP i są kluczowe dla bezpiecznego działania serwisu:

- **Kontakt ze sprzedającym** — przycisk „Napisz do sprzedającego" na stronie
  ogłoszenia jest nieaktywny; nie zbieramy danych kontaktowych ani nie mamy
  mechanizmu kontaktu, co jest rdzeniem wartości tablicy ogłoszeń.
- **Reset hasła** — brak procedury odzyskiwania dostępu do konta.

### Potrzebne
Istotne dla dojrzałej wersji, ale nieblokujące uruchomienia MVP:

- **Wewnętrzny system wiadomości** — sekcja „Wiadomości" w panelu jest dziś
  placeholderem.
- **Weryfikacja adresu e-mail** przy rejestracji.
- **Zarządzanie kategoriami** z panelu administratora (obecnie kategorie
  powstają wyłącznie przez seeder deweloperski) — ostatni brakujący element roli
  administratora.
- **Ulubione / obserwowane ogłoszenia.**

### Nice to have
Rozszerzenia podnoszące wartość serwisu, o niższym priorytecie:

- **Oceny i opinie** o sprzedających.
- **Powiadomienia e-mail** (np. o nowych ogłoszeniach w obserwowanej kategorii).
- **Płatności i promowanie ogłoszeń** (wyróżnienia, podbicia).
- **Weryfikacja NIP** w rejestrze (VIES / biała lista podatników); dziś NIP jest
  jedynie przechowywany.
- **Publiczne API** dla integracji zewnętrznych.

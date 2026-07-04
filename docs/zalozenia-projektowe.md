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

## 2. Odbiorcy i role

| Rola | Opis | Uprawnienia |
| --- | --- | --- |
| **Gość** | Niezalogowany odwiedzający | Przeglądanie i wyszukiwanie ogłoszeń, podgląd szczegółów |
| **Osoba prywatna** | Zarejestrowany użytkownik indywidualny | Wystawianie, edycja i usuwanie własnych ogłoszeń |
| **Firma** | Zarejestrowane konto firmowe (z danymi firmy, np. NIP) | Jak osoba prywatna + oznaczenie ogłoszeń jako firmowe |
| **Administrator** | Obsługa serwisu | Moderacja: ukrywanie/usuwanie ogłoszeń, blokowanie kont, zarządzanie kategoriami |

Rozróżnienie **osoba prywatna / firma** to atrybut konta (typ konta), a nie
osobny model użytkownika. Firma podaje dodatkowe dane (nazwa firmy, NIP,
opcjonalnie adres), które są prezentowane przy jej ogłoszeniach.

## 3. Główne funkcjonalności (MVP)

### Konta i uwierzytelnianie
- Rejestracja i logowanie (e-mail + hasło).
- Wybór typu konta przy rejestracji: **osoba prywatna** lub **firma**.
- Profil użytkownika z jego aktywnymi ogłoszeniami.

### Ogłoszenia
- Utworzenie ogłoszenia: tytuł, opis, cena (opcjonalna / „do negocjacji"),
  kategoria, lokalizacja, dane kontaktowe, zdjęcia.
- Edycja i usuwanie własnych ogłoszeń.
- Status ogłoszenia: `robocze` (draft), `aktywne`, `zakończone`, `ukryte`
  (przez moderację).
- Automatyczne oznaczenie, czy ogłoszenie pochodzi od firmy, czy osoby prywatnej.

### Przeglądanie i wyszukiwanie
- Lista ogłoszeń z paginacją, sortowanie (najnowsze, cena rosnąco/malejąco).
- Filtrowanie po kategorii, lokalizacji, zakresie ceny oraz typie wystawiającego
  (firma / osoba prywatna).
- Wyszukiwanie pełnotekstowe po tytule i opisie.
- Widok szczegółów ogłoszenia z galerią zdjęć i danymi wystawiającego.

### Moderacja (administrator)
- Zgłaszanie ogłoszeń przez użytkowników.
- Ukrywanie/usuwanie ogłoszeń oraz blokowanie kont.
- Zarządzanie słownikiem kategorii.

## 4. Model domenowy (wysokopoziomowo)

Poniżej wstępny zarys encji — będzie doprecyzowywany na etapie projektowania
migracji.

- **User** — konto użytkownika. Pole `account_type` (`private` | `company`)
  określa typ konta. Dane firmowe **nie** są trzymane w tej tabeli.
- **CompanyProfile (Profil firmy)** — dane firmowe wydzielone do osobnej tabeli
  w relacji 1—1 z `User` (istnieje tylko dla kont typu `company`). Pola:
  nazwa firmy, NIP, opcjonalnie adres. NIP jest przechowywany bez walidacji
  w rejestrze (patrz sekcja 6).
- **Listing (Ogłoszenie)** — należy do `User`, do jednej `Category`. Pola:
  tytuł, opis, cena, waluta, lokalizacja, status, znaczniki czasu.
- **Category (Kategoria)** — słownik kategorii **hierarchiczny**: kategoria może
  mieć kategorię nadrzędną (`parent_id`), tworząc drzewo nadrzędna → podkategorie.
  Ogłoszenia przypisywane są zwykle do kategorii liścia.
- **ListingImage (Zdjęcie ogłoszenia)** — należy do `Listing`; przechowuje
  ścieżkę pliku i kolejność.
- **Report (Zgłoszenie)** — zgłoszenie ogłoszenia do moderacji; wiąże
  `User` (zgłaszający) i `Listing`.

Relacje:
- `User 1—1 CompanyProfile` (opcjonalna, tylko dla kont firmowych)
- `User 1—N Listing`
- `Category 1—N Category` (self-relacja: nadrzędna → podkategorie)
- `Category 1—N Listing`
- `Listing 1—N ListingImage`
- `Listing 1—N Report`

## 5. Reguły biznesowe

- Ogłoszenie może wystawić wyłącznie zalogowany użytkownik.
- Użytkownik edytuje/usuwa tylko własne ogłoszenia (poza administratorem).
- Na liście publicznej widoczne są tylko ogłoszenia o statusie `aktywne`.
- Typ wystawiającego (firma / osoba prywatna) jest dziedziczony z konta i
  widoczny przy ogłoszeniu — służy też jako filtr wyszukiwania.
- Konto firmowe wymaga utworzenia profilu firmy (`CompanyProfile`) z nazwą firmy.
  NIP jest przyjmowany bez weryfikacji w rejestrze (patrz sekcja 6).

## 6. Zakres poza MVP (potencjalne rozszerzenia)

Świadomie odkładane na później, aby utrzymać prostotę pierwszej wersji:

- Płatności / promowanie ogłoszeń (wyróżnienia, podbicia).
- Wewnętrzny system wiadomości między użytkownikami.
- Oceny i opinie o sprzedających.
- Powiadomienia e-mail (np. o nowych ogłoszeniach w obserwowanej kategorii).
- Ulubione / obserwowane ogłoszenia.
- Publiczne API dla zewnętrznych integracji.
- **Walidacja/weryfikacja NIP** — w wersji wstępnej NIP jest jedynie
  przechowywany; sprawdzenie poprawności i weryfikacja w rejestrze (np. VIES /
  biała lista podatników) planowane w przyszłości.

## 7. Założenia techniczne

- **Backend:** Laravel 13 (PHP 8.3). Kontrolery zwracają odpowiedzi Inertia
  (`Inertia::render`) zamiast widoków Blade — Laravel pełni rolę warstwy
  aplikacyjnej i API dla frontendu.
- **Frontend:** **Vue 3** jako warstwa widoku, spięta z backendem przez
  **Inertia.js** (model „monolit SPA" — brak osobnego REST API dla własnego
  frontendu, dane przekazywane jako propsy stron Inertia).
- **SSR (Server-Side Rendering):** włączony renderer SSR Inertia/Vue dla lepszego
  SEO ogłoszeń i szybszego pierwszego renderu. Wymaga uruchomienia procesu
  serwera SSR (`php artisan inertia:start-ssr`) obok aplikacji.
- **Build / assety:** **Vite** buduje dwa wejścia — kliencki bundle aplikacji
  oraz bundle SSR. Stylowanie: Tailwind CSS v4 (plugin `@tailwindcss/vite`).
- **Baza danych:** MariaDB (sesje, cache i kolejka na sterowniku `database`).
- **Pliki (zdjęcia):** dysk lokalny w MVP (`storage/`), docelowo możliwy S3.
- **Testy:** PHPUnit (suity `Unit` i `Feature`); testy Feature weryfikują
  odpowiedzi Inertia (komponent + propsy).
- **Uwierzytelnianie:** standardowy mechanizm sesyjny Laravel (Inertia korzysta
  z sesji i ciasteczek, bez tokenów).

> **Status:** stack jest już przygotowany w repozytorium — `inertiajs/inertia-laravel`
> + `@inertiajs/vue3`, konfiguracja SSR oraz kliencki i serwerowy build Vite.
> Strony Vue znajdują się w `resources/js/pages/`. Szczegóły uruchamiania w `CLAUDE.md`.

> Szczegóły środowiska deweloperskiego i poleceń znajdują się w `CLAUDE.md`
> w katalogu głównym projektu.

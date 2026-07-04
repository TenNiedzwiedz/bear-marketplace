<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Development seed data only. Builds the two-level category tree used across the
 * marketplace. Idempotent — safe to re-run.
 */
class CategorySeeder extends Seeder
{
    /**
     * @var array<string, list<string>>
     */
    private array $tree = [
        'Motoryzacja' => ['Samochody', 'Motocykle', 'Części i akcesoria', 'Opony i felgi'],
        'Nieruchomości' => ['Mieszkania', 'Domy', 'Działki', 'Wynajem'],
        'Elektronika' => ['Telefony', 'Komputery', 'RTV i AGD', 'Konsole i gry'],
        'Dom i ogród' => ['Meble', 'Narzędzia', 'Ogród', 'Wyposażenie'],
        'Moda' => ['Odzież damska', 'Odzież męska', 'Obuwie', 'Dodatki'],
        'Dla dzieci' => ['Zabawki', 'Wózki', 'Ubranka', 'Akcesoria'],
        'Sport i hobby' => ['Rowery', 'Fitness', 'Turystyka', 'Instrumenty'],
        'Zwierzęta' => ['Psy', 'Koty', 'Akcesoria dla zwierząt'],
        'Usługi' => ['Remonty', 'Transport', 'Korepetycje', 'Naprawy'],
    ];

    public function run(): void
    {
        $topPosition = 0;

        foreach ($this->tree as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'position' => $topPosition++],
            );

            foreach ($children as $childPosition => $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($parentName.' '.$childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'position' => $childPosition],
                );
            }
        }
    }
}

<?php

use App\Support\Locale;
use Illuminate\Support\Arr;

/**
 * @return array<string, mixed>
 */
function englishStrings(string $group): array
{
    return Arr::dot(require lang_path("en/{$group}.php"));
}

/**
 * @return list<string>
 */
function placeholdersIn(string $text): array
{
    preg_match_all('/:[a-z_]+/', $text, $matches);
    $placeholders = $matches[0];
    sort($placeholders);

    return $placeholders;
}

test('every selectable language translates every English string', function (string $locale) {
    $problems = [];

    foreach (glob(lang_path('en/*.php')) as $file) {
        $group = basename($file, '.php');
        $path = lang_path("{$locale}/{$group}.php");

        if (! is_file($path)) {
            $problems[] = "{$group}.php is missing";

            continue;
        }

        $translated = Arr::dot(require $path);

        foreach (englishStrings($group) as $key => $english) {
            if (! is_string($english) || $english === '') {
                continue;
            }

            $value = $translated[$key] ?? null;

            if (! is_string($value) || trim($value) === '') {
                $problems[] = "{$group}.{$key} is not translated";
            } elseif (placeholdersIn($value) !== placeholdersIn($english)) {
                $problems[] = "{$group}.{$key} has different placeholders";
            } elseif (substr_count($value, '|') !== substr_count($english, '|')) {
                $problems[] = "{$group}.{$key} has different plural forms";
            }
        }
    }

    expect($problems)->toBe([]);
})->with(array_keys(array_diff_key(Locale::LANGUAGES, ['en' => true])));

test('a newly added language renders on the welcome page', function () {
    $this->withSession(['locale' => 'km'])
        ->get('/')
        ->assertOk()
        ->assertSee(__('welcome.nav_services', [], 'km'))
        ->assertSee('ខ្មែរ');
});

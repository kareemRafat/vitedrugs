<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The site's copy is bilingual English/Arabic, so nothing from another writing
 * system can legitimately appear in a language file.
 *
 * This exists because machine translation has already left Chinese inside two
 * Arabic strings — `出路` in the browse-hub subtitle and `认为` in the contact
 * subtitle — and both were committed without anything noticing. A visitor sees
 * those characters as garbage mid-sentence.
 */
class TranslationIntegrityTest extends TestCase
{
    /**
     * Script ranges that have no business in en/ar copy. Arabic (0600-06FF) and
     * Latin are deliberately absent, as is punctuation and the general-punctuation
     * block, since the em dash and Arabic comma are used throughout.
     *
     * @var list<array{0: int, 1: int, 2: string}>
     */
    private const FOREIGN_SCRIPTS = [
        [0x0370, 0x03FF, 'Greek'],
        [0x0400, 0x04FF, 'Cyrillic'],
        [0x0530, 0x058F, 'Armenian'],
        [0x0590, 0x05FF, 'Hebrew'],
        [0x0900, 0x0DFF, 'Indic'],
        [0x0E00, 0x0FFF, 'South-East Asian'],
        [0x1100, 0x11FF, 'Hangul Jamo'],
        [0xAC00, 0xD7AF, 'Hangul'],
        [0x3000, 0x303F, 'CJK punctuation'],
        [0x3040, 0x30FF, 'Kana'],
        [0x3100, 0x312F, 'Bopomofo'],
        [0x3400, 0x9FFF, 'CJK'],
        [0xF900, 0xFAFF, 'CJK compatibility'],
        [0xFF00, 0xFFEF, 'fullwidth'],
    ];

    /**
     * The replacement character is what a botched decode leaves behind whatever
     * the source byte was, so it is checked separately from the script ranges.
     */
    private const REPLACEMENT_CHARACTER = 0xFFFD;

    #[Test]
    public function no_translation_file_contains_a_foreign_script(): void
    {
        $files = glob(lang_path('*/*.php'));

        $this->assertIsArray($files);
        $this->assertNotEmpty($files, 'No language files were found to scan.');

        $offenders = [];

        foreach ($files as $file) {
            $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            $lines = preg_split('/\R/u', (string) file_get_contents($file)) ?: [];

            foreach ($lines as $number => $line) {
                foreach (preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $character) {
                    $codepoint = mb_ord($character);

                    if ($codepoint === false) {
                        continue;
                    }

                    if ($codepoint === self::REPLACEMENT_CHARACTER) {
                        $offenders[] = "{$relative}:".($number + 1).' U+FFFD replacement character';

                        continue;
                    }

                    foreach (self::FOREIGN_SCRIPTS as [$from, $to, $name]) {
                        if ($codepoint >= $from && $codepoint <= $to) {
                            $offenders[] = sprintf(
                                '%s:%d U+%04X (%s)',
                                $relative,
                                $number + 1,
                                $codepoint,
                                $name
                            );
                            break;
                        }
                    }
                }
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "Translation copy contains characters from another writing system:\n".implode("\n", $offenders)
        );
    }
}

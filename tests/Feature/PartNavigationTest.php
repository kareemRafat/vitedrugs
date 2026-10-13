<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Every tool a part has built must be reachable from that part's navbar.
 *
 * This exists because one was not. `/poultry/compare` was shipped, linked from
 * the poultry hub and from nowhere else — no navbar entry, desktop or mobile —
 * while the large-animals half of the same page had one. The page was reachable
 * in exactly one click from the page before it and nowhere else.
 *
 * The expected list is **written out rather than derived from the route table**.
 * Deriving it would compare the navbar against the routes and pass by
 * construction the moment a page was added, which is the failure this test is
 * for. Adding a tool means adding it here, and forgetting is the point.
 */
class PartNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<int, string>>
     */
    private function expectedTools(): array
    {
        return [
            'poultry' => [
                'poultry.diagnosis',
                'poultry.filter',
                'poultry.comparison',
            ],

            'large-animals' => [
                'large-animals.diagnosis',
                'large-animals.filter',
                'large-animals.comparison',
            ],
        ];
    }

    /**
     * The navbar is rendered twice - a desktop bar and a mobile drawer - from two
     * separate blocks in the template. Editing one and not the other is a silent
     * failure, and a plain "is this URL in the HTML?" check **cannot** catch it:
     * both blocks carry the same `href`, so deleting one leaves the other to
     * satisfy the assertion.
     *
     * That is not hypothetical. The first version of this file asserted the URL
     * was present somewhere, and passed with the desktop link deleted.
     *
     * The blocks are told apart by their attributes - desktop marks its links
     * `wire:navigate`, the drawer marks them `onclick="toggleMobileMenu()"` - so
     * each assertion pins exactly one block.
     */
    #[Test]
    public function every_tool_is_linked_from_the_desktop_navbar(): void
    {
        foreach ($this->expectedTools() as $part => $routeNames) {
            $navbar = $this->navbarOf($this->get(route("{$part}.home"))->assertOk()->getContent());

            $this->assertNotNull($navbar, "The {$part} page has no <nav> to assert against.");

            foreach ($routeNames as $name) {
                $this->assertStringContainsString(
                    route($name),
                    (string) $navbar,
                    "{$name} is built but has no link in the {$part} desktop navbar.",
                );
            }
        }
    }

    /**
     * The desktop bar, and only the desktop bar.
     *
     * Returns the `<nav>` element, which closes before the mobile drawer
     * begins — so the two are separated by structure rather than by guessing at
     * attributes.
     *
     * Scoping matters more than it looks. `wire:navigate` alone is **not** a
     * reliable marker: the hub's service cards carry it too, so a check for
     * "an anchor with this href and wire:navigate" is satisfied by page content
     * and passes with the navbar link deleted — which is exactly what happened
     * to the first version of this test.
     */
    private function navbarOf(string $html): ?string
    {
        return preg_match('#<nav\b[^>]*>.*?</nav>#s', $html, $matches) === 1
            ? $matches[0]
            : null;
    }

    /**
     * The mobile drawer, which is a sibling of `<nav>` and carries its own id.
     */
    private function drawerOf(string $html): ?string
    {
        return preg_match('#<div id="mobileSidebar".*?(?=<div id="footer|</body>)#s', $html, $matches) === 1
            ? $matches[0]
            : null;
    }

    #[Test]
    public function every_tool_is_linked_from_the_mobile_drawer(): void
    {
        foreach ($this->expectedTools() as $part => $routeNames) {
            $drawer = $this->drawerOf($this->get(route("{$part}.home"))->assertOk()->getContent());

            $this->assertNotNull($drawer, "The {$part} page has no mobile drawer to assert against.");

            foreach ($routeNames as $name) {
                $this->assertStringContainsString(
                    route($name),
                    (string) $drawer,
                    "{$name} has a desktop navbar link but no mobile drawer link ({$part}).",
                );
            }
        }
    }

    /**
     * The nav label comes from the part's own lang namespace, and the poultry
     * lang file inherits from the large-animals one rather than duplicating it
     * (§9.23). A key added only to the upstream file therefore arrives for free,
     * and a key added only to the child is silently ignored — neither failure
     * shows up as an exception, only as the raw key on the page.
     */
    #[Test]
    public function every_nav_label_resolves_in_both_locales(): void
    {
        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            foreach ($this->expectedTools() as $part => $routeNames) {
                foreach ($routeNames as $name) {
                    $key = str_replace('.', '.nav.', $name);

                    $this->assertNotSame(
                        $key,
                        __($key),
                        "{$key} does not resolve in {$locale}; the navbar would print the raw key.",
                    );
                }
            }
        }
    }
}

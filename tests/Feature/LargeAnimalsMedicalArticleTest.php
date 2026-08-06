<?php

namespace Tests\Feature;

use App\Models\Disease;
use App\Models\LargeAnimals\MedicalArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LargeAnimalsMedicalArticleTest extends TestCase
{
    use RefreshDatabase;

    private function makeArticle(): MedicalArticle
    {
        $disease = Disease::factory()->create([
            'name' => 'Foot and Mouth Disease',
            'name_ar' => 'الحمى القلاعية',
        ]);

        return MedicalArticle::factory()->create([
            'title' => 'Foot-and-Mouth Disease in Large Animals',
            'title_ar' => 'مرض الحمى القلاعية في الحيوانات الكبيرة',
            'slug' => 'foot-and-mouth-disease',
            'content' => '<h2>Overview</h2><p>Introduction.</p><h2>Etiology</h2><p>Viral cause.</p>',
            'content_ar' => '<h2>نظرة عامة</h2><p>مقدمة.</p><h2>المسبب المرضي</h2><p>سبب فيروسي.</p>',
            'summary' => 'An English summary.',
            'summary_ar' => 'ملخص بالعربية.',
            'disease_id' => $disease->id,
            'is_published' => true,
            'species' => 'Cattle, Buffaloes, Sheep, Goats, Pigs',
            'species_ar' => 'الأبقار، الجاموس، الأغنام، الماعز، الخنازير',
        ]);
    }

    public function test_article_page_renders_toc_links_and_section_ids(): void
    {
        $this->makeArticle();

        $this->get(route('large-animals.medical-articles.show', 'foot-and-mouth-disease'))
            ->assertOk()
            ->assertSee('id="section-0"', false)
            ->assertSee('id="section-1"', false)
            ->assertSee('Overview')
            ->assertSee('Etiology');
    }

    public function test_article_uses_arabic_fields_when_locale_is_ar(): void
    {
        $article = $this->makeArticle();

        app()->setLocale('ar');

        $this->assertSame('مرض الحمى القلاعية في الحيوانات الكبيرة', $article->localized_title);
        $this->assertSame('ملخص بالعربية.', $article->localized_summary);
        $this->assertStringContainsString('نظرة عامة', $article->localized_content);
        $this->assertStringContainsString('المسبب المرضي', $article->localized_content);
        $this->assertSame('الأبقار، الجاموس، الأغنام، الماعز، الخنازير', $article->localized_species);

        app()->setLocale('en');

        $this->assertSame('Foot-and-Mouth Disease in Large Animals', $article->localized_title);
        $this->assertStringContainsString('Overview', $article->localized_content);
        $this->assertSame('Cattle, Buffaloes, Sheep, Goats, Pigs', $article->localized_species);
    }

    public function test_unpublished_article_is_not_accessible(): void
    {
        $this->makeArticle()->update(['is_published' => false]);

        $this->get(route('large-animals.medical-articles.show', 'foot-and-mouth-disease'))
            ->assertNotFound();
    }
}

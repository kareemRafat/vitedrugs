<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vetpedia.com',
        ]);

        $categoryData = [
            ['name' => 'Clinical Guides', 'slug' => 'clinical-guides', 'name_ar' => 'أدلة سريرية', 'description' => 'In-depth guides on veterinary clinical practices'],
            ['name' => 'Drug Updates', 'slug' => 'drug-updates', 'name_ar' => 'تحديثات الأدوية', 'description' => 'Latest updates on veterinary pharmaceuticals'],
            ['name' => 'Case Studies', 'slug' => 'case-studies', 'name_ar' => 'دراسات حالة', 'description' => 'Real-world veterinary case studies'],
        ];

        $categories = collect();
        foreach ($categoryData as $data) {
            $categories->push(BlogCategory::firstOrCreate(
                ['slug' => $data['slug']],
                $data,
            ));
        }

        Blog::factory()
            ->count(10)
            ->sequence(fn ($sequence) => [
                'blog_category_id' => $categories->get($sequence->index % 3)->id,
                'author_id' => $admin->id,
            ])
            ->create();

        Blog::factory()
            ->count(2)
            ->draft()
            ->sequence(fn ($sequence) => [
                'blog_category_id' => $categories->get($sequence->index % 3)->id,
                'author_id' => $admin->id,
            ])
            ->create();
    }
}

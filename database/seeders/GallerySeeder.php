<?php

namespace Database\Seeders;

use App\Models\GalleryCategory;
use App\Models\GalleryPhoto;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Rentrée scolaire', 'Journée sportive', 'Remise des prix', 'Vie de classe'];

        foreach ($categories as $name) {
            $category = GalleryCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );

            for ($i = 1; $i <= 3; $i++) {
                GalleryPhoto::create([
                    'gallery_category_id' => $category->id,
                    'title' => "{$name} #{$i}",
                    'image' => PlaceholderImage::make('gallery', $name),
                ]);
            }
        }
    }
}

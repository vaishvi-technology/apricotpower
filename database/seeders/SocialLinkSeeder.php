<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'name' => 'Facebook',
                'share_url_pattern' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                'color' => '#1877F2',
                'sort_order' => 1,
            ],
            [
                'name' => 'X (Twitter)',
                'share_url_pattern' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
                'color' => '#000000',
                'sort_order' => 2,
            ],
            [
                'name' => 'Pinterest',
                'share_url_pattern' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
                'color' => '#E60023',
                'sort_order' => 3,
            ],
            [
                'name' => 'LinkedIn',
                'share_url_pattern' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'color' => '#0A66C2',
                'sort_order' => 4,
            ],
            [
                'name' => 'Email',
                'share_url_pattern' => 'mailto:?subject={title}&body={excerpt}%0A%0A{url}',
                'color' => '#555555',
                'open_in_new_tab' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'WhatsApp',
                'share_url_pattern' => 'https://api.whatsapp.com/send?text={title}%20{url}',
                'color' => '#25D366',
                'sort_order' => 6,
            ],
        ];

        foreach ($links as $link) {
            SocialLink::updateOrCreate(
                ['name' => $link['name']],
                $link
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'contact-us'],
            [
                'title' => 'Contact Us',
                'status' => 'published',
                'content' => '<ul>
<li><strong>Toll Free:</strong> (866) 468-7487 (866-GOT-PITS)</li>
<li><strong>Outside The USA:</strong> 001 + 707-262-1394</li>
<li><strong>Fax:</strong> 707-413-6556</li>
<li><strong>Email:</strong> <a href="mailto:customerservice@apricotpower.com">customerservice@apricotpower.com</a></li>
<li><strong>Office Hours:</strong> Monday-Friday, 9AM-5PM Central</li>
</ul>
<p><em>Outside of office hours, calls are directed to our voicemail and will be returned promptly the following business day.</em></p>',
                'meta_title' => 'Contact Us - Apricot Power',
                'meta_description' => 'Contact Apricot Power. Call us toll free at (866) 468-7487 or email customerservice@apricotpower.com.',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'status' => 'published',
                'content' => '<p><strong>Apricot Power</strong> is your reliable source for quality apricot seeds and <strong>B17</strong> products.</p>
<p><strong>Apricot Power</strong> has been providing apricot seeds since 1999. Over the years our company has grown and now sells more than 100 different products and supplements to health conscious customers around the world. Apricot seeds and <strong>B17</strong> are our top sellers.</p>',
                'meta_title' => 'About Us - Apricot Power',
                'meta_description' => 'Apricot Power has been providing quality apricot seeds and B17 products since 1999.',
            ]
        );
    }
}

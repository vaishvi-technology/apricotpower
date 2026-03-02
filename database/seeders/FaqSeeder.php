<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // B17 & Apricot Seed FAQs
        $b17Category = FaqCategory::create([
            'name' => 'B17 & Apricot Seed FAQs',
            'slug' => 'b17-apricot-seed-faqs',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $b17Faqs = [
            [
                'question' => 'What is B17 and Amygdalin?',
                'answer' => 'Amygdalin is a natural compound found in apricot seeds. It belongs to a group of compounds known as cyanogenic glycosides. These compounds are also present in almonds, lima beans, flax seeds, barley, millet, and the seeds of apples, cherries, and pears.',
            ],
            [
                'question' => 'How much B17 is in each apricot seed?',
                'answer' => 'Approximately 20mg of B17 is found in each of our apricot seeds per lab tests.',
            ],
            [
                'question' => 'How many apricot seeds should I take daily?',
                'answer' => 'Start with one seed per hour. Do not consume more than you would eat of any whole fruit in one sitting. If you experience dizziness, headache, or upset stomach, you are consuming too much too fast.',
            ],
            [
                'question' => 'Can I take apricot seeds and B17 together?',
                'answer' => 'Yes, however they should be spread out at least one hour apart from each other. If you notice any unwanted side effects like dizziness, headache or upset stomach then you\'re consuming too much too fast.',
            ],
            [
                'question' => 'How much B17 should I take daily?',
                'answer' => 'Quantities vary from person to person depending on their needs. Research all products before consumption. Consult your naturopath, homeopathic physician, kinesiologist or physician of choice. As with apricot seeds, always start with a small amount and slowly move to a higher amount if desired.',
            ],
            [
                'question' => 'Isn\'t B17 illegal in the United States?',
                'answer' => 'No, however, making claims of any health benefit has been prohibited by the FDA.',
            ],
        ];

        foreach ($b17Faqs as $index => $faq) {
            Faq::create([
                'faq_category_id' => $b17Category->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        // Product & Storage
        $storageCategory = FaqCategory::create([
            'name' => 'Product & Storage',
            'slug' => 'product-storage',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $storageFaqs = [
            [
                'question' => 'Can I freeze apricot seeds?',
                'answer' => 'We recommend you do not freeze apricot seeds. They are better kept in a cool dry place like the refrigerator or pantry.',
            ],
            [
                'question' => 'How long can I store the apricot seeds?',
                'answer' => 'Up to two years in the refrigerator. To learn more about storing apricot seeds, read our storage guide.',
            ],
            [
                'question' => 'Are your apricot seeds raw?',
                'answer' => 'Yes, our apricot seeds are air dried in the pits at low heats and then shelled and packaged. No other processing has been done.',
            ],
            [
                'question' => 'Can I give these products to my pets?',
                'answer' => 'Many of our customers do and have had no problems. Keep in mind your pet\'s body weight and adjust accordingly, and always check with your veterinarian.',
            ],
        ];

        foreach ($storageFaqs as $index => $faq) {
            Faq::create([
                'faq_category_id' => $storageCategory->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        // General Questions
        $generalCategory = FaqCategory::create([
            'name' => 'General Questions',
            'slug' => 'general-questions',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $generalFaqs = [
            [
                'question' => 'Do you ship outside the United States?',
                'answer' => 'We ship almost everywhere in the world with few legal or logistical exceptions.',
            ],
            [
                'question' => 'How much is shipping?',
                'answer' => 'Costs depend on product weight and destination. Estimates are available after adding items to your cart.',
            ],
            [
                'question' => 'What payment options do you accept besides credit cards?',
                'answer' => 'We accept checks, money orders, and PayPal. Mail payments to: Apricot Power, 13501 Ranch Road 12, Ste 103, Wimberley, TX 78676.',
            ],
            [
                'question' => 'Do you offer wholesale pricing?',
                'answer' => 'Yes! Please visit our <a href="/contact">Contact Us</a> page or call 1-866-468-7487 ext 2 for wholesale inquiries.',
            ],
        ];

        foreach ($generalFaqs as $index => $faq) {
            Faq::create([
                'faq_category_id' => $generalCategory->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}

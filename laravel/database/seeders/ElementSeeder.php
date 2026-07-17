<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elements = collect([
            [
                'name' => 'welcome',
                'title' => 'Welcome Message',
                'content' => <<<WELCOME
<p> With 10 + years ' experience, Young Olive is a Best-in-class firm founded on
strong family values, professional output, and operational excellence,
backed by 28+ years multinational company experience in customer
service by pioneer director.</p>
<p>Our business model boasts of simplicity & effectiveness characterized by
shorter reporting lines that guarantee quick turn-around times, tailor-
made value proposition and outlook to better ways of doing things.</p>
<p>Our employment framework follows the rigors of merit-based assessment
criteria that yields passionate, talented and competent staff keen on
continuous improvement of customer-centric service delivery.</p>
WELCOME,
                'type' => 'richtext',
                'photo' => "elements/welcome.jpg",
                'icon' => null,
                'published' => true,
            ],
            [
                'name' => 'who-we-are',
                'title' => 'Who We Are',
                'content' => 'We are a values-driven supportive company that partners with
industry players to drive optimal outcomes in occupational
health, safety, promotional, and apparel solutions. Our values
include',
                'type' => 'multiline',
                'photo' => 'elements/who_we_are.jpg',
                'icon' => null,
                'published' => true,
            ],
            [
                'name' => 'honesty',
                'title' => 'Honesty',
                'content' => 'Honesty',
                'type' => 'text',
                'photo' => 'elements/honesty.jpg',
                'icon' => 'shield-check', // Represents trust and truth
                'published' => true,
            ],
            [
                'name' => 'accountability',
                'title' => 'Accountability',
                'content' => 'Accountability',
                'type' => 'text',
                'photo' => 'elements/accountability.jpg',
                'icon' => 'clipboard-document-check', // Symbolizes responsibility
                'published' => true,
            ],
            [
                'name' => 'integrity',
                'title' => 'Integrity',
                'content' => 'Integrity',
                'type' => 'text',
                'photo' => 'elements/integrity.jpg',
                'icon' => 'scale', // Symbol of fairness and moral balance
                'published' => true,
            ],
            [
                'name' => 'reliability',
                'title' => 'Reliability',
                'content' => 'Reliability',
                'type' => 'text',
                'photo' => 'elements/reliability.jpg',
                'icon' => 'clock', // Suggests consistency and dependability
                'published' => true,
            ],
            [
                'name' => 'team-spirit',
                'title' => 'Team Spirit',
                'content' => 'Team Spirit',
                'type' => 'text',
                'photo' => 'elements/team-spirit.jpg',
                'icon' => 'users', // Represents collaboration and unity
                'published' => true,
            ],
            [
                'name' => 'location',
                'title' => 'Our Location',
                'content' => <<<ADDRESS
Yogi Corp Business Centre, 3rd Floor,Suite 3B, Factory Street, Off Commercial Street, Industrial area
ADDRESS,
                'type' => 'multiline',
                'photo' => 'elements/location.jpg',
                'icon' => 'map-pin', // Indicates a physical location
                'published' => true,
            ],
            [
                'name' => 'email',
                'title' => 'Contact Email',
                'content' => 'info@youngolive.co.ke | sales@youngolive.co.ke',
                'type' => 'text',
                'photo' => 'elements/email.jpg',
                'icon' => 'envelope', // Represents email communication
                'published' => true,
            ],
            [
                'name' => 'phone',
                'title' => 'Contact Phone',
                'content' => '+254722298105 | +254722720859',
                'type' => 'text',
                'photo' => 'elements/phone.jpg',
                'icon' => 'phone', // Symbolizes telephone communication
                'published' => true,
            ],
            [
                'name' => 'address',
                'title' => 'Postal Address',
                'content' => 'P.O. Box 4180-00506, Nairobi, Kenya',
                'type' => 'multiline',
                'photo' => 'elements/address.jpg',
                'icon' => 'inbox-stack', // Represents a physical address
                'published' => true,
            ],
            [
                'name' => 'acorn',
                'title' => 'Acorn Logo',
                'content' => 'The acorn symbolizes growth, potential, and strength',
                'type' => 'text',
                'photo' => 'customers/acorn.png',
                'icon' => null,
                'published' => true,
            ],
            [
                'name' => 'equinox',
                'title' => 'Equinox Logo',
                'content' => 'The equinox represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/equinox.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'highchem-group',
                'title' => 'HigChem Group Logo',
                'content' => 'The highchem-group represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/highchem-group.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'kpc',
                'title' => 'KPC Logo',
                'content' => 'The kpc represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/kpc.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'lavington-security',
                'title' => 'Lavington Security Logo',
                'content' => 'The lavington-security represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/lavington-security.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'ngong-veg',
                'title' => 'Ngong Veg Logo',
                'content' => 'The ngong-veg represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/ngong-veg.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'tamu',
                'title' => 'Tamu Logo',
                'content' => 'The tamu represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/tamu.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'yara',
                'title' => 'Yara Logo',
                'content' => 'The yara represents balance and harmony. At Young Olive',
                'type' => 'text',
                'photo' => 'customers/yara.png',
                'icon' => null,
                'published' => true
            ],
            [
                'name' => 'facebook',
                'title' => 'Facebook Page',
                'content' => 'https://www.facebook.com/',
                'type' => 'text',
                'photo' => null,
                'icon' => 'facebook', // closest to FB speech bubble style
                'published' => true,
            ],
            [
                'name' => 'twitter',
                'title' => 'Twitter Profile',
                'content' => 'https://twitter.com/',
                'type' => 'text',
                'photo' => null,
                'icon' => 'twitter', // Heroicons doesn't include 'bird', use 'paper-airplane' as best fit
                'published' => true,
            ],
            [
                'name' => 'linkedin',
                'title' => 'LinkedIn Profile',
                'content' => 'https://www.linkedin.com/',
                'type' => 'text',
                'photo' => null,
                'icon' => 'linkedin', // business-related
                'published' => true,
            ],
            [
                'name' => 'instagram',
                'title' => 'Instagram Profile',
                'content' => 'https://www.instagram.com/',
                'type' => 'text',
                'photo' => null,
                'icon' => 'instagram', // camera icon fits Instagram
                'published' => true,
            ],
            [
                'name' => 'youtube',
                'title' => 'YouTube Channel',
                'content' => 'https://www.youtube.com/',
                'type' => 'text',
                'photo' => null,
                'icon' => 'youtube', // video play symbol
                'published' => true,
            ],
            [
                'name' => 'products',
                'title' => 'Products',
                'content' => 250,
                'type' => 'text',
                'photo' => null,
                'icon' => null,
                'published' => true,
            ],
            [
                'name' => 'customers',
                'title' => 'Customers',
                'content' => 1200,
                'type' => 'text',
                'photo' => null,
                'icon' => null,
                'published' => true,
            ],
            [
                'name' => 'partners',
                'title' => 'Partners',
                'content' => 10,
                'type' => 'text',
                'photo' => null,
                'icon' => null,
                'published' => true,
            ],
        ]);

        if (Storage::disk('public')->exists('elements')) {
            Storage::disk('public')->deleteDirectory('elements');
        }

        Storage::disk('public')->makeDirectory('elements');

        $elements->each(
            function ($element) {
                $path = $element['photo'] ? storage_path('data/images/' . $element['photo']) : null;

                $filename = file_exists($path) ? \Illuminate\Support\Facades\Storage::disk('public')->putFile(
                    'elements',
                    $path,
                ) : null;

                $name = $element['name'];
                $title = $element['title'];
                $content = $element['content'];
                $type = $element['type'];
                $icon = $element['icon'];
                $published = $element['published'];

                $element = new \Gogilo\PageSections\Models\Element();

                $element->name = $name;
                $element->title = $title;
                $element->content = $content;
                $element->type = $type;
                $element->photo = $filename;
                $element->icon = $icon;
                $element->published = $published;

                $element->save();
                $this->command->info('Stored picture: ' . $filename . ' for element: ' . $title);
            }
        );

        $sections = collect([
            [
                'name' => 'core-values',
                'title' => 'Core Values',
                'description' => 'Core Values',
                'elements' => [
                    'honesty',
                    'accountability',
                    'integrity',
                    'reliability',
                    'team-spirit'
                ]
            ],
            [
                'name' => 'contact-information',
                'title' => 'Contact Information',
                'description' => 'Contact Information',
                'elements' => [
                    'location',
                    'email',
                    'phone',
                    'address'
                ]
            ],
            [
                'name' => 'featured-customers',
                'title' => 'Featured Customers',
                'description' => 'Featured Customers',
                'elements' => [
                    "acorn",
                    "equinox",
                    'highchem-group',
                    'kpc',
                    'lavington-security',
                    'ngong-veg',
                    'tamu',
                    'yara',
                ],
            ],
            [
                'name' => 'socialmedia',
                'title' => 'Social Media',
                'description' => 'Social Media',
                'elements' => [
                    "facebook",
                    "twitter",
                    "linkedin",
                    "instagram",
                    "youtube",
                ]
            ],
            [
                'name' => 'numbers',
                'title' => 'Numbers',
                'description' => 'Fun Facts',
                'elements' => [
                    'products',
                    'customers',
                    'partners',
                ]
            ]
        ]);

        $sections->each(
            function ($section) {
                $pageSection = new \Gogilo\PageSections\Models\PageSection();
                $pageSection->name = $section['name'];
                $pageSection->title = $section['title'];
                $pageSection->description = $section['description'];
                $pageSection->save();
                $elementIds = \Gogilo\PageSections\Models\Element::whereIn('name', $section['elements'])->pluck('id')->toArray();
                $pageSection->elements()->sync($elementIds);
            }
        );
    }
}

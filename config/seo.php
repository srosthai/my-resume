<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canonical site URL
    |--------------------------------------------------------------------------
    | Used for canonical tags, OG urls and the sitemap. Pinned to the production
    | domain via the SEO_URL env var so it stays correct even when APP_URL points
    | at a local host (e.g. me.test).
    */
    'url' => rtrim(env('SEO_URL', 'https://srosthai.dev'), '/'),

    // Brand / site name shown in titles and og:site_name.
    'name' => env('SEO_NAME', 'SROS THAI'),

    'locale' => env('SEO_LOCALE', 'en_US'),

    // Default meta description used when a page does not supply its own.
    'description' => 'SROS THAI — Full Stack Developer from Cambodia, building scalable web apps with Laravel, Vue.js and modern technologies.',

    'keywords' => 'SROS THAI, Sros Thai, srosthai, sros, sovann thai, full stack developer, Laravel developer, Vue.js developer, Cambodia developer, software engineer portfolio',

    /*
    |--------------------------------------------------------------------------
    | Person identity (schema.org Person — drives name-search ranking)
    |--------------------------------------------------------------------------
    */
    'person' => [
        'name' => 'SROS THAI',
        'alternate_names' => ['Sros Thai', 'srosthai', 'Sros', 'Thai Sros', 'Sovann Thai'],
        'job_title' => 'Full Stack Developer',
        'email' => 'srosthai00@gmail.com',
        'locality' => 'Phnom Penh',
        'country' => 'Cambodia',
        'knows_about' => ['Laravel', 'Vue.js', 'PHP', 'JavaScript', 'TypeScript', 'Inertia.js', 'Web Development'],
        // Profiles for schema sameAs — strongest off-site entity signal.
        'same_as' => [
            'https://github.com/srosthai',
            'https://www.linkedin.com/in/sros-thai-b491b42ab/',
            'https://www.facebook.com/srosthai2003',
            'https://www.instagram.com/sovannthai887',
            'https://t.me/srosthai2003',
        ],
    ],

    // Twitter/X handle for twitter:site (leave null if none).
    'twitter' => env('SEO_TWITTER', null),

    // Fallback share image (1200x630) relative to the site root, used when no
    // profile photo is available.
    'default_image' => '/og-image.jpg',
];

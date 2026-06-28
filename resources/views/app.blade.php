<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'light') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        {{-- Title is supplied per-page by Inertia <Head> via @inertiaHead (SSR). --}}

        {{-- Server-rendered SEO baseline: present without JS so crawlers always
             get identity, canonical and structured data. Per-page title/description
             /og are managed by Inertia <Head> (and SSR). --}}
        <meta name="author" content="{{ $seo['person']['name'] }}">
        <meta name="keywords" content="{{ $seo['keywords'] }}">
        <meta name="robots" content="index, follow, max-image-preview:large">
        <meta name="theme-color" content="#000000">
        <link rel="canonical" href="{{ $seo['canonical'] }}">

        <meta property="og:site_name" content="{{ $seo['name'] }}">
        <meta property="og:locale" content="{{ $seo['locale'] }}">
        <meta property="og:url" content="{{ $seo['canonical'] }}">
        <meta property="og:image" content="{{ $seo['person']['image'] }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $seo['person']['image'] }}">
        @if($seo['twitter'])<meta name="twitter:site" content="{{ $seo['twitter'] }}">@endif

        <script type="application/ld+json">
        @php
            echo json_encode([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Person',
                        '@id' => $seo['url'] . '/#person',
                        'name' => $seo['person']['name'],
                        'alternateName' => $seo['person']['alternate_names'],
                        'url' => $seo['url'],
                        'image' => $seo['person']['image'],
                        'jobTitle' => $seo['person']['job_title'],
                        'email' => 'mailto:' . $seo['person']['email'],
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => $seo['person']['locality'],
                            'addressCountry' => $seo['person']['country'],
                        ],
                        'knowsAbout' => $seo['person']['knows_about'],
                        'sameAs' => $seo['person']['same_as'],
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => $seo['url'] . '/#website',
                        'name' => $seo['name'],
                        'url' => $seo['url'],
                        'description' => $seo['description'],
                        'inLanguage' => 'en',
                        'publisher' => ['@id' => $seo['url'] . '/#person'],
                        'author' => ['@id' => $seo['url'] . '/#person'],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @endphp
        </script>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

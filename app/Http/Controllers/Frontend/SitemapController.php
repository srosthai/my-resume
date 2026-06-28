<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Render the XML sitemap for public pages and project detail pages.
     */
    public function index(): Response
    {
        $base = rtrim(config('seo.url'), '/');

        // Static public pages: [path, changefreq, priority].
        $static = [
            ['', 'weekly', '1.0'],
            ['about', 'monthly', '0.9'],
            ['portfolio', 'weekly', '0.9'],
            ['resume', 'monthly', '0.8'],
            ['note', 'weekly', '0.7'],
            ['feeds', 'weekly', '0.6'],
            ['hobby', 'monthly', '0.5'],
            ['contact', 'monthly', '0.6'],
            ['more', 'monthly', '0.4'],
        ];

        $urls = [];
        foreach ($static as [$path, $freq, $priority]) {
            $urls[] = [
                'loc' => $base . ($path !== '' ? '/' . $path : ''),
                'changefreq' => $freq,
                'priority' => $priority,
                'lastmod' => null,
            ];
        }

        Project::query()
            ->select(['id', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->each(function (Project $project) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/portfolio/' . $project->id,
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                    'lastmod' => optional($project->updated_at)->toAtomString(),
                ];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}

<?php

declare(strict_types=1);

namespace Yatsn\Styles;

use Yatsn\Support\Database;

final class StyleService
{
    public static function seed(): int
    {
        $launch = [
            ['photoreal_cinema', 'Cinematic Realism', 'Cinematic', 'Broad premium cinematic storytelling.'],
            ['oil_painting_realism', 'Heirloom Oil Portrait', 'Fine Art', 'Romantic and giftable oil portraiture.'],
            ['watercolor', 'Luminous Watercolor', 'Fine Art', 'Soft emotional watercolor atmosphere.'],
            ['impressionist_painting', 'Impressionist Light', 'Fine Art', 'Painterly light for romantic songs.'],
            ['art_deco', 'Art Deco Elegance', 'Era / Glamour', 'Geometric glamour for celebrations.'],
            ['indie_pop_pastel', 'Indie Pastel Dream', 'Contemporary', 'Gentle modern pastel mood.'],
            ['jazz_expressionism', 'Midnight Jazz', 'Music / Fine Art', 'Club light and expressive brushwork.'],
            ['psychedelic_rock', 'Psychedelic Reverie', 'Music / Poster', 'Vivid music-driven energy.'],
            ['punk_collage', 'Punk Zine Collage', 'Music / Collage', 'Raw handmade alternative aesthetic.'],
            ['synthpop_1980s_neon_airbrush', 'Neon Synth Dream', 'Music / Retro', 'Retro neon airbrush atmosphere.'],
            ['cyberpunk_neon_noir', 'Neon Noir', 'Cinematic / Sci-Fi', 'Dramatic night-world cinema.'],
            ['dreamcore', 'Liminal Dream', 'Surreal', 'Symbolic dreamlike spaces.'],
            ['collage_surrealism', 'Surreal Story Collage', 'Surreal / Collage', 'Layered narrative collage.'],
            ['gothic_darkwave', 'Gothic Romance', 'Dark / Romantic', 'Elegant dark romantic imagery.'],
            ['fantasy_epic_illustration', 'Epic Fantasy', 'Fantasy', 'High-adventure illustrated worlds.'],
        ];

        $inactive = [
            ['fantasy_metal_poster_alestorm', 'Fantasy Metal Poster', 'Music / Fantasy'],
            ['pixel_art_legendary', 'Legendary Pixel Quest', 'Pixel / Fantasy'],
            ['anime', 'Cel-Shaded Adventure', 'Illustration'],
            ['metal_fantasy', 'Heavy Metal Fantasy', 'Music / Fantasy'],
            ['vaporwave', 'Vaporwave Nostalgia', 'Retro / Surreal'],
            ['grunge_90s', '1990s Grunge', 'Music / Collage'],
            ['hiphop_graffiti', 'Hip-Hop Graffiti', 'Music / Street Art'],
            ['minimal_techno', 'Minimal Techno Geometry', 'Music / Graphic'],
            ['expressionist', 'Raw Expressionism', 'Fine Art'],
            ['charcoal_ink_sumi_e', 'Charcoal and Ink', 'Fine Art'],
            ['jazz_deco_1920s', '1920s Jazz Deco', 'Era / Music'],
            ['wpa_swing_1930s', '1930s Swing Poster', 'Era / Music'],
            ['big_band_1940s_portrait', '1940s Big-Band Portrait', 'Era / Portrait'],
            ['rockabilly_1950s_americana', '1950s Rockabilly', 'Era / Music'],
            ['motown_1960s_studio', '1960s Soul-Studio Glamour', 'Era / Music'],
            ['krautrock_1970s_minimal', '1970s Motorik Minimalism', 'Era / Music'],
            ['prog_rock_1970s_surrealism', '1970s Progressive Surrealism', 'Era / Music'],
            ['disco_1970s_glam_airbrush', '1970s Disco Glamour', 'Era / Music'],
            ['reggae_roots_1970s_poster', '1970s Roots Poster', 'Era / Music'],
            ['new_wave_1980s_memphis', '1980s New-Wave Geometry', 'Era / Music'],
            ['hardcore_punk_1980s_xerox', '1980s Hardcore Xerox', 'Era / Music'],
            ['shoegaze_1990s_blur', '1990s Shoegaze Haze', 'Era / Music'],
            ['triphop_1990s_noir', '1990s Trip-Hop Noir', 'Era / Music'],
            ['black_metal_1990s_photocopy', '1990s Black-Metal Photocopy', 'Era / Music'],
            ['indie_2000s_minimal_swiss', '2000s Indie Minimalism', 'Era / Music'],
            ['y2k_pop_chrome_2000s', 'Y2K Chrome Pop', 'Era / Pop'],
            ['edm_2010s_festival_neon', '2010s Festival Neon', 'Era / Electronic'],
            ['kpop_2010s_hypergloss', '2010s Hypergloss Pop', 'Era / Pop'],
            ['hyperpop_2020s_glitch_candy', 'Glitch-Candy Hyperpop', 'Era / Pop'],
            ['retrofuturism_70s_airbrush', 'Retro-Future Airbrush', 'Sci-Fi / Retro'],
            ['sci_fi_holographic_ui', 'Holographic Science Fiction', 'Sci-Fi'],
            ['space_opera_pulp', 'Pulp Space Opera', 'Sci-Fi / Illustration'],
            ['dark_fantasy_baroque', 'Baroque Dark Fantasy', 'Fantasy / Dark'],
            ['steampunk_brass_gauges', 'Brasswork Steampunk', 'Fantasy / Industrial'],
            ['dieselpunk_industrial', 'Industrial Dieselpunk', 'Sci-Fi / Industrial'],
            ['spaghetti_western_poster', 'Western Cinema Poster', 'Cinematic / Era'],
            ['giallo_italian_thriller_poster', 'Saturated Thriller Cinema', 'Cinematic / Era'],
        ];

        $now = now_utc();
        $count = 0;
        $order = 1;
        foreach ($launch as $style) {
            $count += self::upsert($style[0], $style[1], $style[3], $style[2], $order++, 'active', $now);
        }
        foreach ($inactive as $style) {
            $count += self::upsert($style[0], $style[1], 'Recovered V1 family reserved for later activation.', $style[2], $order++, 'inactive', $now);
        }
        return $count;
    }

    public static function activeForClient(): array
    {
        $rows = Database::all(
            'SELECT * FROM styles WHERE status = \'active\' ORDER BY sort_order ASC, id ASC'
        );
        return array_map([self::class, 'public'], $rows);
    }

    public static function allForOwner(): array
    {
        $rows = Database::all('SELECT * FROM styles ORDER BY sort_order ASC, id ASC');
        return array_map([self::class, 'public'], $rows);
    }

    public static function findByPublicId(string $publicId): ?array
    {
        return Database::one('SELECT * FROM styles WHERE public_id = :pid', ['pid' => $publicId]);
    }

    public static function setStatus(string $publicId, string $status): array
    {
        if (!in_array($status, ['active', 'inactive'], true)) {
            throw new \InvalidArgumentException('invalid_status');
        }
        $row = self::findByPublicId($publicId);
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        $now = now_utc();
        Database::exec(
            'UPDATE styles SET status = :s, updated_at = :u WHERE id = :id',
            ['s' => $status, 'u' => $now, 'id' => $row['id']]
        );
        $updated = Database::one('SELECT * FROM styles WHERE id = :id', ['id' => $row['id']]);
        return self::public($updated);
    }

    public static function productOptions(): array
    {
        return [
            'orientations' => [
                ['id' => 'square', 'label' => 'Square'],
                ['id' => 'portrait', 'label' => 'Portrait'],
                ['id' => 'landscape', 'label' => 'Landscape'],
            ],
            'qualities' => [
                ['id' => 'low', 'label' => 'Low', 'helper' => 'Uses fewer credits', 'credits' => \Yatsn\Credits\CreditService::priceForQuality('low')],
                ['id' => 'medium', 'label' => 'Medium', 'helper' => 'Recommended', 'credits' => \Yatsn\Credits\CreditService::priceForQuality('medium')],
                ['id' => 'high', 'label' => 'High', 'helper' => 'Our most advanced option', 'credits' => \Yatsn\Credits\CreditService::priceForQuality('high')],
            ],
            'noTextInImage' => [
                'default' => false,
                'label' => 'No text in image',
                'helper' => 'Choose this if you want the finished artwork to contain no words or lettering.',
            ],
            'limits' => [
                'maxPortraitsPerGeneration' => 2,
                'maxSavedPortraits' => 10,
                'maxSpecialInstructionsChars' => 500,
            ],
            'monthlyMembership' => [
                'priceCents' => 2000,
                'currency' => 'USD',
                'name' => 'You Are The Song Now Membership',
                'statementDescriptor' => 'YOU ARE THE SONG',
                'monthlyCredits' => \Yatsn\Support\Config::getInt('credits.development_monthly'),
            ],
        ];
    }

    public static function public(?array $row): array
    {
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        return [
            'id' => $row['public_id'],
            'styleKey' => $row['style_key'],
            'name' => $row['name'],
            'description' => $row['description'],
            'category' => $row['category'],
            'status' => $row['status'],
            'sortOrder' => (int) $row['sort_order'],
            'promptVersion' => $row['prompt_version'],
        ];
    }

    private static function upsert(
        string $key,
        string $name,
        string $description,
        string $category,
        int $order,
        string $status,
        string $now
    ): int {
        $existing = Database::one('SELECT id FROM styles WHERE style_key = :k', ['k' => $key]);
        if ($existing !== null) {
            Database::exec(
                'UPDATE styles SET name = :n, description = :d, category = :c, sort_order = :o, status = :s, updated_at = :u WHERE id = :id',
                ['n' => $name, 'd' => $description, 'c' => $category, 'o' => $order, 's' => $status, 'u' => $now, 'id' => $existing['id']]
            );
            return 0;
        }
        Database::exec(
            'INSERT INTO styles (public_id, style_key, name, description, category, sort_order, status, created_at, updated_at)
             VALUES (:pid, :k, :n, :d, :c, :o, :s, :c_at, :u)',
            [
                'pid' => opaque_id(),
                'k' => $key,
                'n' => $name,
                'd' => $description,
                'c' => $category,
                'o' => $order,
                's' => $status,
                'c_at' => $now,
                'u' => $now,
            ]
        );
        return 1;
    }
}

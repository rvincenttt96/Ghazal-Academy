<?php

function inja_packages(): array
{
    require_once __DIR__ . '/packages.php';
    return ghazal_live_packages();
}

/**
 * Ghazal title => Inja product id. null means we know it is not sold there.
 */
function inja_title_map(): array
{
    return [
        'Phonics Book' => 11046,
        'Phonics Pamphlet' => null,
        'A Day with a Baby' => null,
        'Little Helpers' => 9466,
        'Where Is It' => 9424,
        'P1 Package' => null,
        'Doctor Doctor' => 9413,
        'My Family' => 9418,
        'Monkeying Around' => 9414,
        'Silly Squirrel' => 9412,
        'Backpack 2' => 10518,
        'Just Like Mine' => 9476,
        'Things That Fly' => 9479,
        'Backpack Pamphlet' => null,
        'Backpack 3' => 10519,
        'What Did You Do Yesterday' => 9478,
        'Backpack 4' => 10520,
        'New Girl in School' => 9474,
        'Up and Down' => 9517,
        "Let's Go 2" => 10946,
        'Picture Dictionary' => 10981,
        'Jingle Bells' => null,
        "Let's Go Pamphlet" => null,
        'Lost' => 9472,
        'Numbers Numbers' => 56446,
        "Let's Go 3" => 10974,
        'Wonderful Wild Animals' => 9475,
        'Students in Space' => 9477,
        "Let's Go 4" => 10976,
        'In the Ocean' => 9483,
        "Let's Go 5" => 10978,
        'Tough Task' => 9482,
        "Let's Go 6" => 10980,
        'City Girl' => 9485,
        'Talk Time 1' => null,
        'Talk Time Pamphlet' => null,
        'Talk Time 2' => null,
        'Talk Time 3' => null,
        'Top Notch Fundamentals A' => 10229,
        'Top Notch Fundamentals B' => 10230,
        'Top Notch Fundamentals Pamphlet' => null,
        'Top Notch 1A' => 10231,
        'Top Notch 1B' => 10232,
        'Top Notch 1 Pamphlet' => null,
        'Phrasal Verbs Intermediate' => 12573,
        'Top Notch 2A' => 10233,
        'Top Notch 2B' => 10234,
        'Top Notch 2 Pamphlet' => null,
        'Top Notch 3A' => 10235,
        'Top Notch 3B' => 10276,
        'Top Notch 3 Pamphlet' => null,
        'Summit 1A' => 10225,
        'Summit 1B' => 10226,
        'Summit 1 Pamphlet' => null,
        'Phrasal Verbs Advanced' => 12572,
        'Summit 2A' => 10227,
        'Summit 2B' => 10228,
        'Summit 2 Pamphlet' => null,
        'Ready for First' => 36244,
        'Ready for CAE' => 36242,
        'Objective Proficiency' => null,
    ];
}

function inja_format_toman($amount): string
{
    if ($amount === null || $amount === '') {
        return '—';
    }
    return number_format((int)$amount, 0, '.', ',') . ' تومان';
}

function inja_http_get(string $url, int $timeout = 18): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: GhazalBookStore/1.0',
        ],
    ]);
    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $errno || $http >= 400) {
        return [];
    }
    $json = json_decode((string)$raw, true);
    return is_array($json) ? $json : [];
}

function inja_normalize_product(array $product): array
{
    $prices = is_array($product['prices'] ?? null) ? $product['prices'] : [];
    $price = isset($prices['price']) ? (int)$prices['price'] : 0;
    $regular = isset($prices['regular_price']) ? (int)$prices['regular_price'] : $price;
    $sale = isset($prices['sale_price']) ? (int)$prices['sale_price'] : 0;
    $inStock = !empty($product['is_in_stock']);
    $purchasable = array_key_exists('is_purchasable', $product)
        ? (bool)$product['is_purchasable']
        : !empty($product['add_to_cart']['url']);
    return [
        'id' => (int)($product['id'] ?? 0),
        'name' => (string)($product['name'] ?? ''),
        'permalink' => (string)($product['permalink'] ?? ''),
        'price' => $price,
        'regular_price' => $regular,
        'sale_price' => $sale,
        'on_sale' => !empty($product['on_sale']) && $sale > 0 && $sale < $regular,
        'in_stock' => $inStock,
        'purchasable' => $purchasable,
        'image' => (string)($product['images'][0]['thumbnail'] ?? $product['images'][0]['src'] ?? ''),
    ];
}

function inja_fetch_products_by_ids(array $ids): array
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if ($ids === []) {
        return [];
    }
    $url = 'https://injabook.com/wp-json/wc/store/v1/products?' . http_build_query([
        'include' => implode(',', $ids),
        'per_page' => max(count($ids), 1),
        'orderby' => 'include',
    ]);
    $list = inja_http_get($url);
    $out = [];
    foreach ($list as $item) {
        if (!is_array($item)) {
            continue;
        }
        $row = inja_normalize_product($item);
        if ($row['id'] > 0) {
            $out[$row['id']] = $row;
        }
    }
    return $out;
}

function inja_search_products(string $query, int $limit = 12): array
{
    $query = trim($query);
    if ($query === '') {
        return [];
    }
    $url = 'https://injabook.com/wp-json/wc/store/v1/products?' . http_build_query([
        'search' => $query,
        'per_page' => $limit,
    ]);
    $list = inja_http_get($url);
    $out = [];
    foreach ($list as $item) {
        if (is_array($item)) {
            $out[] = inja_normalize_product($item);
        }
    }
    return $out;
}

function inja_empty_row(string $title, $mappedId): array
{
    return [
        'title' => $title,
        'mapped_id' => $mappedId,
        'found' => false,
        'product' => null,
        'reason' => $mappedId === null ? 'در اینجا بوک نیست' : 'الان از اینجا بوک خوانده نشد',
    ];
}

function inja_build_catalog_payload(array $products): array
{
    $packages = [];
    $foundTitles = [];
    $missingTitles = [];
    $map = inja_title_map();

    foreach (inja_packages() as $code => $books) {
        $rows = [];
        $sum = 0;
        $priced = 0;
        foreach ($books as $title) {
            $id = array_key_exists($title, $map) ? $map[$title] : null;
            if ($id === null) {
                $rows[] = inja_empty_row($title, null);
                $missingTitles[$title] = true;
                continue;
            }
            if (!isset($products[$id])) {
                $rows[] = inja_empty_row($title, $id);
                $missingTitles[$title] = true;
                continue;
            }
            $product = $products[$id];
            $rows[] = [
                'title' => $title,
                'mapped_id' => $id,
                'found' => true,
                'product' => $product,
                'reason' => '',
            ];
            $foundTitles[$title] = true;
            unset($missingTitles[$title]);
            if ($product['price'] > 0) {
                $sum += $product['price'];
                $priced++;
            }
        }
        $packages[] = [
            'code' => $code,
            'title' => $code . ' package',
            'rows' => $rows,
            'sum' => $sum,
            'priced_count' => $priced,
            'book_count' => count($books),
        ];
    }

    return [
        'fetched_at' => date('Y-m-d H:i:s'),
        'found' => count($foundTitles),
        'missing' => count($missingTitles),
        'packages' => $packages,
    ];
}

function inja_rebuild_catalog_cache(): void
{
    $products = [];
    $cached = get_setting('inja_prices_cache');
    if (is_string($cached) && $cached !== '') {
        $json = json_decode($cached, true);
        if (is_array($json)) {
            foreach ($json['packages'] ?? [] as $pkg) {
                foreach ($pkg['rows'] ?? [] as $row) {
                    $p = $row['product'] ?? null;
                    if (is_array($p) && (int)($p['id'] ?? 0) > 0) {
                        $products[(int)$p['id']] = $p;
                    }
                }
            }
        }
    }
    $need = [];
    foreach (inja_title_map() as $id) {
        if (is_int($id) && $id > 0 && !isset($products[$id])) {
            $need[] = $id;
        }
    }
    if ($need !== []) {
        foreach (inja_fetch_products_by_ids($need) as $id => $product) {
            $products[(int)$id] = $product;
        }
    }
    $payload = inja_build_catalog_payload($products);
    $payload['from_cache'] = false;
    $payload['cache_age'] = 0;
    $payload['live_ok'] = $products !== [];
    set_setting('inja_prices_cache', json_encode($payload, JSON_UNESCAPED_UNICODE));
    set_setting('inja_prices_cached_at', (string)time());
}

function inja_get_catalog_prices(bool $force = false): array
{
    $ttl = 20 * 60;
    if (!$force) {
        $cached = get_setting('inja_prices_cache');
        $cachedAt = (int)get_setting('inja_prices_cached_at', 0);
        if (is_string($cached) && $cached !== '' && $cachedAt > 0 && (time() - $cachedAt) < $ttl) {
            $json = json_decode($cached, true);
            if (is_array($json) && !empty($json['packages'])) {
                $json['from_cache'] = true;
                $json['cache_age'] = time() - $cachedAt;
                return $json;
            }
        }
    }

    $ids = [];
    foreach (inja_title_map() as $id) {
        if (is_int($id) && $id > 0) {
            $ids[] = $id;
        }
    }
    $products = inja_fetch_products_by_ids($ids);
    $payload = inja_build_catalog_payload($products);
    $payload['from_cache'] = false;
    $payload['cache_age'] = 0;
    $payload['live_ok'] = $products !== [];

    if ($payload['live_ok']) {
        set_setting('inja_prices_cache', json_encode($payload, JSON_UNESCAPED_UNICODE));
        set_setting('inja_prices_cached_at', (string)time());
        return $payload;
    }

    $cached = get_setting('inja_prices_cache');
    if (is_string($cached) && $cached !== '') {
        $json = json_decode($cached, true);
        if (is_array($json) && !empty($json['packages'])) {
            $json['from_cache'] = true;
            $json['live_ok'] = false;
            $json['cache_age'] = time() - (int)get_setting('inja_prices_cached_at', 0);
            return $json;
        }
    }

    $payload['error'] = 'الان قیمت‌های اینجا بوک خوانده نشد.';
    return $payload;
}

function inja_attach_sell_prices(array $catalog): array
{
    require_once __DIR__ . '/sell-prices.php';
    $overrides = ghazal_override_map();
    $packages = $catalog['packages'] ?? [];
    $pricedTitles = [];
    $missingTitles = [];
    $manualCount = 0;
    $seenManual = [];
    foreach ($packages as $pIdx => $package) {
        $sellSum = 0;
        $sellCount = 0;
        $rows = $package['rows'] ?? [];
        foreach ($rows as $rIdx => $row) {
            $title = (string)($row['title'] ?? '');
            $injaPrice = 0;
            if (!empty($row['found']) && is_array($row['product'] ?? null)) {
                $injaPrice = (int)($row['product']['price'] ?? 0);
            }
            $kind = ghazal_book_kind($title);
            $computed = ghazal_formula_sell_price($title, $injaPrice);
            $override = $overrides[$title] ?? null;
            $isManual = $override !== null && (int)$override > 0;
            $sell = ghazal_sell_price($title, $injaPrice, $isManual ? $override : null);
            $rows[$rIdx]['kind'] = $kind;
            $rows[$rIdx]['inja_price'] = $injaPrice > 0 ? $injaPrice : null;
            $rows[$rIdx]['computed_price'] = $computed;
            $rows[$rIdx]['override_price'] = $isManual ? (int)$override : null;
            $rows[$rIdx]['is_manual'] = $isManual;
            $rows[$rIdx]['sell_price'] = $sell;
            if ($isManual && !isset($seenManual[$title])) {
                $seenManual[$title] = true;
                $manualCount++;
            }
            if ($sell !== null) {
                $sellSum += $sell;
                $sellCount++;
                $pricedTitles[$title] = true;
                unset($missingTitles[$title]);
            } elseif (!isset($pricedTitles[$title])) {
                $missingTitles[$title] = true;
            }
        }
        $packages[$pIdx]['rows'] = $rows;
        $packages[$pIdx]['sell_sum'] = $sellSum;
        $packages[$pIdx]['sell_count'] = $sellCount;
    }
    $catalog['packages'] = $packages;
    $catalog['sell_found'] = count($pricedTitles);
    $catalog['sell_missing'] = count($missingTitles);
    $catalog['manual_count'] = $manualCount;
    return $catalog;
}

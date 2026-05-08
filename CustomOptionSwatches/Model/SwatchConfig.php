<?php
namespace Bodylanguage\CustomOptionSwatches\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class SwatchConfig
{
    public const XML_PATH_ENABLED = 'bodylanguage_customoptionswatches/general/enabled';
    public const XML_PATH_SWATCH_SIZE = 'bodylanguage_customoptionswatches/general/swatch_size';
    public const XML_PATH_FALLBACK_COLOR = 'bodylanguage_customoptionswatches/general/fallback_color';
    public const XML_PATH_COLOR_MAPPINGS = 'bodylanguage_customoptionswatches/general/color_mappings';
    public const XML_PATH_PATTERN_MAPPINGS = 'bodylanguage_customoptionswatches/general/pattern_mappings';
    public const XML_PATH_SWATCH_OPTION_LABELS = 'bodylanguage_customoptionswatches/general/swatch_option_labels';

    private const DEFAULT_SWATCH_SIZE = 60;
    private const DEFAULT_FALLBACK_COLOR = '#C7CED6';

    /**
     * @var string[]
     */
    private const DEFAULT_SWATCH_OPTION_LABELS = [
        'color',
        'colors',
    ];

    /**
     * @var string[]
     */
    private const DEFAULT_COLOR_FAMILIES = [
        'baby blue' => '#8FCFFF',
        'baby pink' => '#F8B7D0',
        'black' => '#111111',
        'brown' => '#7A4B2E',
        'burgundy' => '#7B1E3A',
        'chartreuse' => '#B7D500',
        'chocolate' => '#5B3A29',
        'coral' => '#FF7F6E',
        'fuchsia' => '#E5007D',
        'gold' => '#C9A227',
        'green' => '#2F8F4E',
        'hot pink' => '#FF4FA3',
        'jade' => '#00A86B',
        'lavender' => '#B497D6',
        'lilac' => '#C8A2D1',
        'lime' => '#9EDB39',
        'magenta' => '#D70087',
        'neon green' => '#39FF14',
        'neon orange' => '#FF6A00',
        'neon pink' => '#FF2DAA',
        'neon yellow' => '#E8FF1A',
        'nude' => '#D6B7A1',
        'orange' => '#FF8C1A',
        'pink' => '#F58DB2',
        'purple' => '#7A4BC2',
        'red' => '#C91F2E',
        'royal' => '#2155D6',
        'royal blue' => '#1E4ED8',
        'silver' => '#B8BEC9',
        'turquoise' => '#20C6C7',
        'white' => '#F7F7F7',
        'yellow' => '#F3D229',
    ];

    /**
     * @var string[]
     */
    private const DEFAULT_COLOR_ALIASES = [
        'as shown' => 'white',
        'b pink' => 'baby pink',
        'b blue white lace' => 'baby blue',
        'b pink white lace' => 'baby pink',
        'bab' => 'baby blue',
        'baby blue rainbow' => 'baby blue',
        'baby blue f' => 'baby blue',
        'baby blue white lace' => 'baby blue',
        'baby pink lace' => 'baby pink',
        'baby pink rainbow' => 'baby pink',
        'baby pink f' => 'baby pink',
        'bany blue' => 'baby blue',
        'black as shown' => 'black',
        'black lame' => 'black',
        'black silver fringe' => 'black',
        'brown j' => 'brown',
        'burgandy' => 'burgundy',
        'burgangy' => 'burgundy',
        'burgundi' => 'burgundy',
        'camouflage print' => 'green',
        'camouflaged' => 'green',
        'chartruese' => 'chartreuse',
        'chartruse' => 'chartreuse',
        'chartuse' => 'chartreuse',
        'cheeta' => 'brown',
        'cheeta print' => 'brown',
        'cheetah' => 'brown',
        'cheetah print' => 'brown',
        'fiuchsia' => 'fuchsia',
        'flame' => 'orange',
        'fuschia' => 'fuchsia',
        'gold lame' => 'gold',
        'gold sequin' => 'gold',
        'lilac silver' => 'lilac',
        'liquid foil gold' => 'gold',
        'liquid foil silver' => 'silver',
        'm l' => 'silver',
        'magento' => 'magenta',
        'magneto' => 'magenta',
        'mangenta' => 'magenta',
        'metallic foil lime' => 'lime',
        'metallic foil pink' => 'pink',
        'multi color swirl' => 'pink',
        'multi purple' => 'purple',
        'multi silver' => 'silver',
        'neon green lace' => 'neon green',
        'neon pink lace' => 'neon pink',
        'o s' => 'silver',
        'pink plaid' => 'pink',
        'purple silver fring' => 'purple',
        'rainbow hologram' => 'white',
        'red lace' => 'red',
        'red plaid' => 'red',
        'red red fringe' => 'red',
        'roual' => 'royal',
        'royal blue plaid' => 'royal blue',
        'royal blue royal blue sequins' => 'royal blue',
        'royal f' => 'royal',
        'royal roya' => 'royal',
        'silver back' => 'silver',
        'sliver white' => 'silver',
        'solid black' => 'black',
        'solid lime' => 'lime',
        'solid neon yellow' => 'neon yellow',
        'solid pink' => 'pink',
        'solid turquoise' => 'turquoise',
        'stars' => 'silver',
        'turquiose' => 'turquoise',
        'turquise' => 'turquoise',
        'white lace' => 'white',
        'white rainbow' => 'white',
    ];

    /**
     * @var string[]
     */
    private const DEFAULT_PATTERN_MAPPINGS = [
        'camouflage' => 'pattern-camo',
        'camouflage black' => 'pattern-camo',
        'camouflage print' => 'pattern-camo',
        'camouflaged' => 'pattern-camo',
        'cheeta' => 'pattern-leopard',
        'cheeta print' => 'pattern-leopard',
        'cheetah' => 'pattern-leopard',
        'cheetah print' => 'pattern-leopard',
        'leopard' => 'pattern-leopard',
        'leopard black' => 'pattern-leopard',
        'zebra' => 'pattern-zebra',
        'zebra black' => 'pattern-zebra',
        'zebra hot pink' => 'pattern-zebra',
        'zebra print' => 'pattern-zebra',
        'zebra turquoise' => 'pattern-zebra',
    ];

    /**
     * @var string[]
     */
    private const PATTERN_KEYWORDS = [
        'camouflage' => 'pattern-camo',
        'camouflaged' => 'pattern-camo',
        'camo' => 'pattern-camo',
        'leopard' => 'pattern-leopard',
        'cheetah' => 'pattern-leopard',
        'cheeta' => 'pattern-leopard',
        'zebra' => 'pattern-zebra',
    ];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $resolvedCache = [];

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return int
     */
    public function getSwatchSize($storeId = null)
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_SWATCH_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value > 0 ? $value : self::DEFAULT_SWATCH_SIZE;
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getFallbackColor($storeId = null)
    {
        $value = strtoupper((string) $this->scopeConfig->getValue(
            self::XML_PATH_FALLBACK_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        return $this->isHexColor($value) ? $value : self::DEFAULT_FALLBACK_COLOR;
    }

    /**
     * @param int|string|null $storeId
     * @return array
     */
    public function getSwatchOptionLabels($storeId = null)
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_SWATCH_OPTION_LABELS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($value === '') {
            return self::DEFAULT_SWATCH_OPTION_LABELS;
        }

        $labels = preg_split('/[\r\n,]+/', $value);
        $labels = array_map('trim', $labels);
        $labels = array_filter($labels, static function ($row) {
            return trim((string) $row) !== '';
        });
        return array_map([$this, 'normalizeLabel'], $labels);
    }

    /**
     * @param string $title
     * @return bool
     */
    public function canRenderOptionTitle($title)
    {
        $normalized = $this->normalizeLabel($title);

        if ($normalized === '') {
            return false;
        }

        // Render size-based option titles as swatches as well.
        if (strpos($normalized, 'size') !== false) {
            return true;
        }

        foreach ($this->getSwatchOptionLabels() as $label) {
            if ($label !== '' && strpos($normalized, $label) !== false) {
                return true;
            }
        }

        return strpos($normalized, 'color') !== false;
    }

    /**
     * @param string $title
     * @return string|null
     */
    public function getSwatchOptionType($title)
    {
        $normalized = $this->normalizeLabel($title);
        $labels = $this->getSwatchOptionLabels();

        foreach ($labels as $label) {
            if ($label === 'size' && strpos($normalized, 'size') !== false) {
                return 'size';
            }
        }

        if (strpos($normalized, 'size') !== false) {
            return 'size';
        }

        return 'color';
    }

    /**
     * @param string $label
     * @param int|string|null $storeId
     * @return array
     */
    public function resolveSwatch($label, $storeId = null)
    {
        $rawLabel = trim((string) $label);
        $normalized = $this->normalizeLabel($label);
        $resolved = $this->getResolvedMappings($storeId);

        if ($this->isHexColor($rawLabel)) {
            return $this->buildColorSwatch($rawLabel, $label, $normalized);
        }

        if ($normalized === '') {
            return $this->buildColorSwatch($this->getFallbackColor($storeId), $label, $normalized);
        }

        if (isset($resolved['pattern_exact'][$normalized])) {
            return $this->buildPatternSwatch($resolved['pattern_exact'][$normalized], $label, $normalized);
        }

        if (isset($resolved['image_exact'][$normalized])) {
            return $this->buildImageSwatch($resolved['image_exact'][$normalized], $label, $normalized);
        }

        if (isset($resolved['color_exact'][$normalized])) {
            return $this->buildColorSwatch($resolved['color_exact'][$normalized], $label, $normalized);
        }

        foreach ($this->getCandidates($normalized) as $candidate) {
            if (isset($resolved['pattern_exact'][$candidate])) {
                return $this->buildPatternSwatch($resolved['pattern_exact'][$candidate], $label, $normalized);
            }

            if (isset($resolved['image_exact'][$candidate])) {
                return $this->buildImageSwatch($resolved['image_exact'][$candidate], $label, $normalized);
            }

            if (isset($resolved['color_exact'][$candidate])) {
                return $this->buildColorSwatch($resolved['color_exact'][$candidate], $label, $normalized);
            }
        }

        $pattern = $this->matchByKeyword($normalized, $resolved['pattern_keywords']);
        if ($pattern !== null) {
            return $this->buildPatternSwatch($pattern, $label, $normalized);
        }

        $image = $this->matchByKeyword($normalized, $resolved['image_keywords']);
        if ($image !== null) {
            return $this->buildImageSwatch($image, $label, $normalized);
        }

        $color = $this->matchByKeyword($normalized, $resolved['color_keywords']);
        if ($color !== null) {
            return $this->buildColorSwatch($color, $label, $normalized);
        }

        foreach ($this->getCandidates($normalized) as $candidate) {
            $pattern = $this->matchByKeyword($candidate, $resolved['pattern_keywords']);
            if ($pattern !== null) {
                return $this->buildPatternSwatch($pattern, $label, $normalized);
            }

            $image = $this->matchByKeyword($candidate, $resolved['image_keywords']);
            if ($image !== null) {
                return $this->buildImageSwatch($image, $label, $normalized);
            }

            $color = $this->matchByKeyword($candidate, $resolved['color_keywords']);
            if ($color !== null) {
                return $this->buildColorSwatch($color, $label, $normalized);
            }
        }

        return $this->buildColorSwatch($this->getFallbackColor($storeId), $label, $normalized);
    }

    /**
     * @param string $label
     * @return string
     */
    public function normalizeLabel($label)
    {
        $normalized = strtolower((string) $label);
        $normalized = str_replace(['"', "'", '&'], ['', '', ' and '], $normalized);
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim((string) $normalized);
    }

    /**
     * @param int|string|null $storeId
     * @return array
     */
    private function getResolvedMappings($storeId)
    {
        $cacheKey = (string) ($storeId ?? 'default');
        if (isset($this->resolvedCache[$cacheKey])) {
            return $this->resolvedCache[$cacheKey];
        }

        $colorExact = [];
        foreach (self::DEFAULT_COLOR_FAMILIES as $label => $hex) {
            $colorExact[$this->normalizeLabel($label)] = strtoupper($hex);
        }
        foreach (self::DEFAULT_COLOR_ALIASES as $label => $family) {
            if (isset(self::DEFAULT_COLOR_FAMILIES[$family])) {
                $colorExact[$this->normalizeLabel($label)] = self::DEFAULT_COLOR_FAMILIES[$family];
            }
        }

        $patternExact = [];
        foreach (self::DEFAULT_PATTERN_MAPPINGS as $label => $pattern) {
            $patternExact[$this->normalizeLabel($label)] = $pattern;
        }

        $imageExact = [];
        foreach ($this->getAdminRows(self::XML_PATH_COLOR_MAPPINGS, $storeId) as $row) {
            $label = $this->normalizeLabel($row['option_label'] ?? '');
            $color = strtoupper(trim((string) ($row['color'] ?? '')));
            $image = trim((string) ($row['image'] ?? ''));

            if ($label !== '' && $this->isHexColor($color)) {
                $colorExact[$label] = $color;
            }

            if ($label !== '' && $image !== '') {
                $imageExact[$label] = [
                    'image' => $image,
                    'color' => $this->isHexColor($color) ? $color : $this->getFallbackColor($storeId),
                ];
            }
        }

        foreach ($this->getAdminRows(self::XML_PATH_PATTERN_MAPPINGS, $storeId) as $row) {
            $label = $this->normalizeLabel($row['option_label'] ?? '');
            $pattern = trim((string) ($row['pattern'] ?? ''));
            if ($label !== '' && $pattern !== '') {
                $patternExact[$label] = $pattern;
            }
        }

        $colorKeywords = $this->sortKeywords($colorExact);
        $patternKeywords = $this->sortKeywords(array_merge(self::PATTERN_KEYWORDS, $patternExact));
        $imageKeywords = $imageExact
            ? $this->sortKeywords(array_combine(array_keys($imageExact), array_column($imageExact, 'image')))
            : [];

        $this->resolvedCache[$cacheKey] = [
            'color_exact' => $colorExact,
            'pattern_exact' => $patternExact,
            'image_exact' => $imageExact,
            'color_keywords' => $colorKeywords,
            'pattern_keywords' => $patternKeywords,
            'image_keywords' => $imageKeywords,
        ];

        return $this->resolvedCache[$cacheKey];
    }

    /**
     * @param string $path
     * @param int|string|null $storeId
     * @return array
     */
    private function getAdminRows($path, $storeId)
    {
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);

        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param string $normalized
     * @return array
     */
    private function getCandidates($normalized)
    {
        $candidates = [];
        $segments = preg_split('/\s+(?:and|with)\s+|\/+/', $normalized);

        foreach ((array) $segments as $segment) {
            $segment = $this->normalizeLabel($segment);
            if ($segment !== '' && $segment !== $normalized) {
                $candidates[] = $segment;
            }
        }

        $words = explode(' ', $normalized);
        if (!empty($words)) {
            $firstTwo = $this->normalizeLabel(implode(' ', array_slice($words, 0, 2)));
            $firstOne = $this->normalizeLabel($words[0]);

            foreach ([$firstTwo, $firstOne] as $candidate) {
                if ($candidate !== '' && $candidate !== $normalized) {
                    $candidates[] = $candidate;
                }
            }
        }

        return array_values(array_unique($candidates));
    }

    /**
     * @param string $normalized
     * @param array $keywordMap
     * @return string|null
     */
    private function matchByKeyword($normalized, array $keywordMap)
    {
        $winner = null;
        $winnerPosition = null;
        $winnerLength = -1;

        foreach ($keywordMap as $keyword => $value) {
            $position = strpos($normalized, $keyword);
            if ($position === false) {
                continue;
            }

            $length = strlen($keyword);
            if ($winner === null
                || $position < $winnerPosition
                || ($position === $winnerPosition && $length > $winnerLength)
            ) {
                $winner = $value;
                $winnerPosition = $position;
                $winnerLength = $length;
            }
        }

        return $winner;
    }

    /**
     * @param array $keywordMap
     * @return array
     */
    private function sortKeywords(array $keywordMap)
    {
        uksort($keywordMap, function ($left, $right) {
            return strlen($right) <=> strlen($left);
        });

        return $keywordMap;
    }

    /**
     * @param string $color
     * @param string $label
     * @param string $normalized
     * @return array
     */
    private function buildColorSwatch($color, $label, $normalized)
    {
        return [
            'type' => 'color',
            'value' => strtoupper($color),
            'css_class' => '',
            'style' => 'background-color: ' . strtoupper($color) . ';',
            'label' => $label,
            'normalized_label' => $normalized,
        ];
    }

    /**
     * @param string $pattern
     * @param string $label
     * @param string $normalized
     * @return array
     */
    private function buildPatternSwatch($pattern, $label, $normalized)
    {
        return [
            'type' => 'pattern',
            'value' => $pattern,
            'css_class' => $pattern,
            'style' => '',
            'label' => $label,
            'normalized_label' => $normalized,
        ];
    }

    /**
     * @param array|string $imageMap
     * @param string $label
     * @param string $normalized
     * @return array
     */
    private function buildImageSwatch($imageMap, $label, $normalized)
    {
        if (is_array($imageMap)) {
            $image = (string) ($imageMap['image'] ?? '');
            $fallbackColor = (string) ($imageMap['color'] ?? $this->getFallbackColor());
        } else {
            $image = (string) $imageMap;
            $fallbackColor = $this->getFallbackColor();
        }

        $imageUrl = $this->getImageUrl($image);
        $style = 'background-color: ' . strtoupper($fallbackColor) . ';'
            . ' background-image: url(' . $imageUrl . ');'
            . ' background-size: cover; background-position: center; background-repeat: no-repeat;';

        return [
            'type' => 'image',
            'value' => $image,
            'css_class' => 'custom-swatch-image',
            'style' => $style,
            'label' => $label,
            'normalized_label' => $normalized,
        ];
    }

    /**
     * @param string $image
     * @return string
     */
    private function getImageUrl($image)
    {
        $image = trim((string) $image);
        if ($image === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $image)) {
            return $image;
        }

        $image = ltrim($image, '/');
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return rtrim($baseUrl, '/') . '/' . $image;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isHexColor($value)
    {
        return (bool) preg_match('/^#[0-9A-F]{6}$/i', $value);
    }
}

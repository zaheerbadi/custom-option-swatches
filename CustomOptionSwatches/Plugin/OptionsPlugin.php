<?php
namespace Bodylanguage\CustomOptionSwatches\Plugin;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Block\Product\View\Options\Type\Select;
use Bodylanguage\CustomOptionSwatches\Model\SwatchConfig;
use Bodylanguage\CustomOptionSwatches\Model\SwatchConfigProvider;

class OptionsPlugin
{
    /**
     * @var SwatchConfigProvider
     */
    private $configProvider;

    /**
     * @param SwatchConfigProvider $configProvider
     */
    public function __construct(SwatchConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * Add swatch data to the select option block before rendering.
     *
     * @param Select $subject
     * @return void
     */
    public function beforeToHtml(Select $subject)
    {
        $config = $this->configProvider->getConfig();
        $option = $subject->getOption();

        if (!$option
            || !$config->isEnabled()
            || $option->getType() !== ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN
        ) {
            return;
        }

        // Build candidate swatch data from option values
        $swatchData = $this->buildSwatchData($option, $config);

        // Determine whether this option should render as swatches.
        // Use title hint when present; otherwise require at least 75% of values to resolve to swatch types.
        $total = count($swatchData);
        $swatchCount = 0;
        foreach ($swatchData as $d) {
            if (isset($d['type']) && in_array($d['type'], ['color', 'pattern', 'image'], true)) {
                $swatchCount++;
            }
        }

        // Only apply swatches when the option title explicitly indicates a color.
        // This prevents non-color dropdowns (e.g., Size) from being rendered as swatches.
        $useTitleHint = $config->canRenderOptionTitle($option->getTitle());
        $useSwatches = ($useTitleHint && $swatchCount > 0);

        if (!$useSwatches) {
            return;
        }

        $subject->setData('swatch_data', $swatchData);
        $subject->setData('swatch_size', $config->getSwatchSize());
    }

    /**
     * Build swatch data for a dropdown option.
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param SwatchConfig $config
     * @return array
     */
    private function buildSwatchData($option, $config)
    {
        $swatchData = [];

        foreach ((array) $option->getValues() as $value) {
            $definition = $config->resolveSwatch($value->getTitle());
            $swatchData[] = [
                'id' => $value->getId(),
                'label' => $value->getTitle(),
                'type' => $definition['type'],
                'value' => $definition['value'],
                'style' => $definition['style'],
                'css_class' => $definition['css_class'],
                'normalized_label' => $definition['normalized_label'],
            ];
        }

        return $swatchData;
    }
}

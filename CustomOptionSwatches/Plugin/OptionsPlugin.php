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

        $supportedTypes = [
            ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN,
            ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE,
        ];

        if (!$option || !$config->isEnabled() || !in_array($option->getType(), $supportedTypes, true)) {
            // Always clear swatch data to prevent bleed-through from previous options
            $subject->setData('swatch_data', null);
            $subject->setData('swatch_type', null);
            return;
        }

        // Build candidate swatch data from option values
        $swatchData = $this->buildSwatchData($option, $config);

        // Determine whether this option should render as swatches.
        $total = count($swatchData);
        $swatchCount = 0;
        foreach ($swatchData as $d) {
            if (isset($d['type']) && in_array($d['type'], ['color', 'pattern', 'image'], true)) {
                $swatchCount++;
            }
        }

        // Only apply swatches when the option title explicitly indicates a color.
        $useTitleHint = $config->canRenderOptionTitle($option->getTitle());
        $useSwatches = ($useTitleHint && $swatchCount > 0);

        if (!$useSwatches) {
            // Clear swatch data to prevent bleed-through from previous options
            $subject->setData('swatch_data', null);
            $subject->setData('swatch_type', null);
            return;
        }

        $subject->setData('swatch_data', $swatchData);
        $subject->setData('swatch_type', $config->getSwatchOptionType($option->getTitle()));
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

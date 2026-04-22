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
            || !$config->canRenderOptionTitle($option->getTitle())
        ) {
            return;
        }

        $swatchData = $this->buildSwatchData($option, $config);

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

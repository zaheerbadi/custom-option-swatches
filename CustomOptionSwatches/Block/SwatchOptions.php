<?php
namespace Bodylanguage\CustomOptionSwatches\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Bodylanguage\CustomOptionSwatches\Model\SwatchConfigProvider;

class SwatchOptions extends Template
{
    /**
     * @var SwatchConfigProvider
     */
    private $configProvider;

    /**
     * @param Context $context
     * @param SwatchConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        SwatchConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * Get swatch configuration
     *
     * @return \Bodylanguage\CustomOptionSwatches\Model\SwatchConfig
     */
    public function getSwatchConfig()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Get color for a value
     *
     * @param string $value
     * @return string|null
     */
    public function getColorForValue($value)
    {
        return $this->getSwatchConfig()->getColorForValue($value);
    }

    /**
     * Check if value has swatch
     *
     * @param string $value
     * @return bool
     */
    public function hasSwatch($value)
    {
        return $this->getSwatchConfig()->hasSwatch($value);
    }
}

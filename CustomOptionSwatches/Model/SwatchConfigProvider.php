<?php
namespace Bodylanguage\CustomOptionSwatches\Model;

class SwatchConfigProvider
{
    /**
     * @var SwatchConfig
     */
    private $config;

    /**
     * @param SwatchConfig $config
     */
    public function __construct(SwatchConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Get swatch configuration
     *
     * @return SwatchConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}

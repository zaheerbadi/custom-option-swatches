<?php
namespace Bodylanguage\CustomOptionSwatches\Block\Adminhtml\System\Config\Form\Field;

use Bodylanguage\CustomOptionSwatches\Model\Config\Source\PatternOptions as PatternOptionsSource;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class PatternOptions extends Select
{
    /**
     * @var PatternOptionsSource
     */
    private $patternOptions;

    /**
     * @param Context $context
     * @param PatternOptionsSource $patternOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        PatternOptionsSource $patternOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->patternOptions = $patternOptions;
    }

    /**
     * Set input name.
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->patternOptions->toOptionArray() as $option) {
                $this->addOption($option['value'], $option['label']);
            }
        }

        return parent::_toHtml();
    }
}

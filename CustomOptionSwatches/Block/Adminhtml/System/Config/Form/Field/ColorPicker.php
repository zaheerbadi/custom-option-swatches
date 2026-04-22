<?php
namespace Bodylanguage\CustomOptionSwatches\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorPicker extends Field
{
    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setClass(trim($element->getClass() . ' custom-option-swatches-color-picker'));
        $html = parent::_getElementHtml($element);

        return $html . '<script>
            require(["jquery"], function ($) {
                var $input = $("#' . $element->getHtmlId() . '");
                if ($input.length) {
                    $input.attr("type", "color").attr("pattern", "^#[0-9A-Fa-f]{6}$");
                }
            });
        </script>';
    }
}

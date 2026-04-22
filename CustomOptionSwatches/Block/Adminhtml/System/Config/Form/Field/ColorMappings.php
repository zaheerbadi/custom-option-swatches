<?php
namespace Bodylanguage\CustomOptionSwatches\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class ColorMappings extends AbstractFieldArray
{
    /**
     * @inheritDoc
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'option_label',
            [
                'label' => __('Option Label'),
                'class' => 'required-entry',
            ]
        );
        $this->addColumn(
            'color',
            [
                'label' => __('Hex Color'),
                'class' => 'required-entry',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Color Mapping');
    }

    /**
     * @inheritDoc
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName === 'color') {
            $inputName = $this->_getCellInputElementName($columnName);

            return '<input type="color"'
                . ' id="' . $this->_getCellInputElementId('<%- _id %>', $columnName) . '"'
                . ' name="' . $inputName . '"'
                . ' value="<%- ' . $columnName . ' %>"'
                . ' class="required-entry input-text admin__control-text"'
                . ' pattern="^#[0-9A-Fa-f]{6}$"'
                . ' />';
        }

        return parent::renderCellTemplate($columnName);
    }
}

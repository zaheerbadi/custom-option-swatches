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
        $this->addColumn(
            'image',
            [
                'label' => __('Swatch Image'),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Color Mapping');
    }

    /**
     * Override to ensure row ids are valid HTML id selectors and unique across the page.
     * Prefix ids with the element id to avoid conflicts between different field arrays.
     *
     * @return array
     */
    public function getArrayRows()
    {
        $rows = parent::getArrayRows();
        $fixed = [];
        $elementId = $this->getElement()->getId();

        foreach ($rows as $rowId => $row) {
            $currentId = (string) $row->getData('_id');
            if ($currentId === '') {
                $fixed[$rowId] = $row;
                continue;
            }

            $newId = $elementId . '_' . $currentId;

            $rowData = $row->getData();
            $columnValues = $rowData['column_values'] ?? [];
            $newColumnValues = [];

            foreach ($columnValues as $key => $val) {
                $newKey = preg_replace('/^' . preg_quote($currentId, '/') . '_/', $newId . '_', $key, 1);
                $newColumnValues[$newKey] = $val;
            }

            $rowData['column_values'] = $newColumnValues;
            $rowData['_id'] = $newId;

            $fixed[$newId] = new \Magento\Framework\DataObject($rowData);
        }

        return $fixed;
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

        if ($columnName === 'image') {
            $inputName = $this->_getCellInputElementName($columnName);
            $fileInputName = $inputName . '[file]';
            $valueInputName = $inputName . '[value]';
            // Compute media base URL for preview images
            $mediaBase = '';
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
                $mediaBase = rtrim($storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA), '/');
            } catch (\Throwable $e) {
                $mediaBase = '';
            }

            $hiddenId = $this->_getCellInputElementId('<%- _id %>', $columnName);
            $previewId = $hiddenId . '_preview';

            // The FieldArray script will populate the hidden input value after inserting rows.
            // Include an <img> that will be updated to show the uploaded image when present.
            $html = '<div>';
            $html .= '<input type="file" id="' . $this->_getCellInputElementId('<%- _id %>', $columnName) . '_file" name="' . $fileInputName . '" class="input-file admin__control-file" />';
            $html .= '<input type="hidden" id="' . $hiddenId . '" name="' . $valueInputName . '" value="" />';
            $html .= '<img id="' . $previewId . '" src="" data-media-base="' . $this->escapeHtmlAttr($mediaBase) . '" style="max-width:80px; display:none; margin-top:8px; border:1px solid #e5e7eb;" onerror="this.style.display=\'none\'" />';
            $html .= '<span class="note" style="display:block; font-size: 11px; color: #6b7280;">' . __('Optional, uploads are stored in pub/media/bodylanguage/customoption_images') . '</span>';

            // Inline script: update preview after FieldArray sets input values. Uses a short timeout to allow population.
            $script = '<script>(function(){try{var hid=document.getElementById("' . $hiddenId . '");var img=document.getElementById("' . $previewId . '");if(!hid||!img) return; function update(){var v=hid.value||""; if(!v){img.style.display="none"; img.src=""; return;} var base=img.getAttribute("data-media-base")||""; img.src = (base?base+"/":"") + v; img.style.display="block";} setTimeout(update,50); if(hid.addEventListener){hid.addEventListener("change",update);} else {hid.attachEvent&&hid.attachEvent("onchange",update);} }catch(e){} })();</script>';

            $html .= $script . '</div>';

            return $html;
        }

        return parent::renderCellTemplate($columnName);
    }
}

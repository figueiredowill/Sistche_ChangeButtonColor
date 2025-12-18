<?php

namespace Sistche\ChangeButtonColor\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class ButtonColor extends Template
{
    private const XML_PATH_COLOR = 'design/button/color';

    /**
     * Get the button color from configuration
     *
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_COLOR,
            ScopeInterface::SCOPE_STORE
        );
    }
}
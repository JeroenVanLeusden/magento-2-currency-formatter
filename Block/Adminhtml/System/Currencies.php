<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CurrencyFormatter
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CurrencyFormatter\Block\Adminhtml\System;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Mageplaza\CurrencyFormatter\Helper\Data as HelperData;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Test\Helper;

/**
 * Class Currencies
 * @package Mageplaza\CurrencyFormatter\Block\Adminhtml\System
 */
class Currencies extends AbstractFieldArray
{
    const BASE_SELECT_NAME = 'groups[general][fields][currencies][value]';
    
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CurrencyFormatter::system/config/currencies.phtml';
    
    /**
     * @var HelperData
     */
    protected $_helperData;
    
    /**
     * @var CurrencyInterface
     */
    protected $_localeCurrency;
    
    /**
     * Currencies constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('currencies', ['label' => __('Currencies')]);
        $this->addColumn('template', ['label' => __('Template')]);
        $this->addColumn('preview', ['label' => __('Preview')]);
    }
    
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreCurrencies()
    {
        $mpCurrencies = [];
        $storeId = $this->getRequest()->getParam('store', 0);
        $formatOptions = $this->_helperData->getFormatOptions();
        $availableCurrencies = $this->_helperData->getCurrenciesByStore($storeId);
        foreach ($availableCurrencies as $code) {
            $mpCurrencies[$code]['code'] = $code;
            $mpCurrencies[$code]['name'] = $this->_localeCurrency->getCurrency($code)->getName();
            $mpCurrencies[$code]['decimal_number'] = $formatOptions['decimal_number'];
            $mpCurrencies[$code]['decimal_separator'] = $formatOptions['decimal_separator'];
            $mpCurrencies[$code]['group_separator'] = $formatOptions['group_separator'];
            $mpCurrencies[$code]['show_symbol'] = $formatOptions['show_symbol'];
            $mpCurrencies[$code]['show_minus'] = $formatOptions['show_minus'];
            $mpCurrencies[$code]['config'] = $this->getElement()->getValue()[$code];
            $mpCurrencies[$code]['default'] = $this->getUseDefaultText();
            $mpCurrencies[$code]['base'] = self::BASE_SELECT_NAME;
        }
    
        return HelperData::jsonEncode(array_values($mpCurrencies));
    }
    
    /**
     * @return array
     */
    public function getUseDefaultText()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if ($storeId !== 0) {
            return __('Use Website');
        }
        
        $websiteId = (int) $this->getRequest()->getParam('website', 0);
        if ($websiteId !== 0) {
            return __('Use Default');
        }
        
        return __('Use System');
    }
}

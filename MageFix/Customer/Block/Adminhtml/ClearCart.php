<?php

namespace MageFix\Customer\Block\Adminhtml;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ClearCart extends GenericButton implements ButtonProviderInterface
{

    protected $_quote;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Quote\Model\QuoteFactory $quote
    ) {
        parent::__construct($context, $registry);
        $this->_quote = $quote;
    }

    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $quoteCollection = $this->_quote->create();
        $quote = $quoteCollection->loadByCustomer($customerId);
        $quoteItemNumber = $quote->getItemsQty();
        $data = [];
        if ($customerId && !empty($quoteItemNumber)) {
            $emptyCartUrl = $this->getClearCartUrl($customerId, $quote->getId());
            $data = [
                'label' => __('Clear Cart'),
                'class' => 'clear-cart',
                'id' => 'customer-clear-cart-button',
                'data_attribute' => [
                    'url' => $emptyCartUrl
                ],
                'on_click' => sprintf("location.href = '%s';", $emptyCartUrl),
                'sort_order' => 40,
            ];
        }
        return $data;

    }

    public function getClearCartUrl($customerId, $quoteId)
    {
        return $this->getUrl('magefix/cart/delete', ['id' => $customerId, 'quote' => $quoteId]);
    }
}
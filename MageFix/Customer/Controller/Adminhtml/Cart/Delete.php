<?php

namespace MageFix\Customer\Controller\Adminhtml\Cart;

class Delete extends \Magento\Backend\App\Action
{
    protected $_customerRepositoryFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepositoryFactory,
        \Magento\Quote\Model\QuoteFactory $quote
    ) {
        $this->_customerRepositoryFactory = $customerRepositoryFactory;
        $this->_quote = $quote;
        parent::__construct($context);
    }


    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $quoteId = $this->getRequest()->getParam('quote');
        $customerCollection = $this->_customerRepositoryFactory->create();
        $customer = $customerCollection->getById($id);
        if (!$customer->getId()) {
            $this->messageManager->addError(__('Unable to find customer'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customer/index/edit', ['id' => $id]);
        }
        $quoteCollection = $this->_quote->create();
        $quote = $quoteCollection->loadByCustomer($id);
        $quoteItemNumber = $quote->getItemsQty();
        if (!empty($quoteItemNumber) && ($quoteId == $quote->getId())){
            $quote->setIsActive(false)->save();
            $this->messageManager->addSuccess(__('Customer cart has been de-activated.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customer/index/edit', ['id' => $id]);
        }
        $this->messageManager->addError(__('Unable to match cart with customer'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('customer/index/edit', ['id' => $id]);
    }
}

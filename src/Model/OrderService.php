<?php

namespace SajidPatel\SalesOrder\Model;

use Exception;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\EmailAddress;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderService
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var EmailAddress
     */
    private $emailAddressValidator;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * OrderService constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param EmailAddress $emailAddressValidator
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EmailAddress $emailAddressValidator,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        $this->orderRepository = $orderRepository;
        $this->emailAddressValidator = $emailAddressValidator;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
    }


    public function getOrders($field, $value)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($field, $value, 'like')->create();
        return $this->orderRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param $orderId
     * @return OrderInterface
     */
    public function getOrder($orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * @param $emailAddress
     * @return OrderInterface[]
     */
    public function searchByEmail($emailAddress)
    {
        return $this->getOrders('customer_email', $emailAddress);
    }

    /**
     * @param $id
     * @param $currentEmail
     * @param $newEmail
     * @return Phrase
     * @throws Exception
     */
    public function sendNewEmail($id, $currentEmail, $newEmail)
    {
        $order = $this->getOrder($id);
        if (!$this->emailAddressValidator->isValid($newEmail)) {
            throw new Exception(__('Invalid email provided'));
        }

        if ($order->getId() && $order->getCustomerEmail() == $currentEmail) {
            $comment = __("Order email address has changed for order: %1 from %2 to %3", $id, $currentEmail, $newEmail);
            $order->setCustomerEmail($newEmail);
            $order->addStatusHistoryComment($comment);
            $this->orderRepository->save($order);

            return $comment;
        }
        return __("Order email address update failed.");
    }
}

<?php declare(strict_types=1);

namespace SajidPatel\OrderEmail\Model;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\Exception;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderService constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param EmailAddress $emailAddressValidator
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EmailAddress $emailAddressValidator,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ExtensibleDataObjectConverter $dataObjectConverter,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->emailAddressValidator = $emailAddressValidator;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->logger = $logger;
    }

    /**
     * @param $field
     * @param $value
     * @return OrderInterface[]
     */
    public function getOrders($field, $value)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($field, $value, 'like')->create();
        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $this->logInteraction('Orders retrieved with Order IDs : ' . implode(array_keys($orders), ','));

        return $orders;
    }

    /**
     * @param $orderId
     * @return OrderInterface
     */
    public function getOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $this->logInteraction('Order retrieved for Order ID : ' . $order->getId());
        return $order;
    }

    /**
     * @param $emailAddress
     * @return OrderInterface[]
     */
    public function searchByEmail($emailAddress)
    {
        $this->logInteraction('Email: ' . $emailAddress, 'info');
        return $this->getOrders('customer_email', $emailAddress);
    }

    /**
     * @param $order
     * @param $currentEmail
     * @param $newEmail
     * @return Phrase
     * @throws Exception
     */
    public function setNewEmail($order, $currentEmail, $newEmail)
    {
        if (!$this->emailAddressValidator->isValid($newEmail)) {
            $error = __('Invalid email provided');
            $this->logInteraction($error, 'error');
            throw new Exception($error);
        }

        if ($order->getId() && $order->getCustomerEmail() == $currentEmail) {
            $comment = __("Order email address has changed for order: %1 from %2 to %3", $order->getId(), $currentEmail, $newEmail);
            $order->setCustomerEmail($newEmail);
            $order->addStatusHistoryComment($comment);
            $this->orderRepository->save($order);

            $this->logInteraction($comment);
            return $comment;
        }
        $comment = __("Order email address update failed.");
        $this->logInteraction($comment);
        return $comment;
    }

    public function logInteraction($message, $severity = 'info')
    {
        $message = (is_object($message) ? $message->render() : $message);
        $this->logger->{$severity}(strip_tags($message));
    }
}

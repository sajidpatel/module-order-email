<?php declare(strict_types=1);

namespace SajidPatel\OrderEmail\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\DataObject as FrameworkDataObject;
use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\Exception;
use Magento\Sales\Api\Data\OrderInterface;
use SajidPatel\OrderEmail\Model\Exception\InputValidationException;
use SajidPatel\OrderEmail\Model\OrderService;
use SajidPatel\OrderEmail\Model\UserValidationRules;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OrderEmailCommand extends Command
{
    const COMMAND_NAME = 'ruroc:order:update-email';
    const TITLE = 'Order Email Update';
    const KEY_ORDER_ID = 'order_id';
    const KEY_EMAIL = 'email';
    const ALL_ORDERS = 'Update All Orders';
    const SELECT_ORDER_ID_PREFIX = 'Order id: ';
    const OPTION_PROMPT = 'Please choose an update option.';
    const OPTION_BY_EMAIL = 'Update by email address';
    const OPTION_BY_EMAIL_PROMPT = 'Please enter current email address';
    const OPTION_BY_ORDER_ID = 'Update by order ID';
    const OPTION_BY_ORDER_ID_PROMPT = 'Please enter an Order ID';
    const OPTION_QUIT = 'quit';
    const OPTION_QUIT_PROMPT = 'Quit current process';
    const OPTION_ALL = 'all';

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var UserValidationRules
     */
    protected $userValidationRules;

    /**
     * @var SymfonyStyle
     */
    protected $symfonyStyle;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var bool
     */
    protected $quit = false;

    public function __construct(
        OrderService $orderService,
        UserValidationRules $userValidationRules,
        State $state,
        string $name = null
    ) {
        $this->userValidationRules = $userValidationRules;
        $this->appState = $state;
        $this->orderService = $orderService;

        parent::__construct($name);
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setAliases(['update-email'])
            ->setDescription('Update an existing order\'s email.')
            ->setHelp($this->getHelpDescription())
            ->setDefinition($this->getOptionsList());

        parent::configure();
    }

    /**
     * Creation admin user in interaction mode.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this, 'updateOrderCallback'],
                [$input, $output]
            );
        } catch (\Exception $e) {
            $this->quit = true;
            $this->output->writeln(__('There has been an error in accessing console command ruroc:order:update-email'));
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->quit) {
            $this->interact($this->input, $this->output);
        }
        return CLI::RETURN_SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function updateOrderCallback(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->input = $input;
            $this->output = $output;

            $this->symfonyStyle = new SymfonyStyle($this->input, $this->output);

            $response = $this->topLevelQuestion();
            $this->orderService->logInteraction($response);
            switch ($response) {
                case self::OPTION_BY_EMAIL:
                    $orders = $this->updateByEmail();
                    $this->selectOrderToUpdate($orders);
                    break;
                case self::OPTION_BY_ORDER_ID:
                    $order = $this->updateByOrderId();
                    $this->updateOrderEmail($order);
                    break;
                case self::OPTION_QUIT:
                    $this->symfonyStyle->writeln(__('<info>Thank you</info>'));
                    $this->quit = true;
                    break;
                default:
                    throw new InputValidationException('Invalid selection!');
            }
        } catch (InputValidationException | Exception | \Exception $e) {
            $this->quit = true;
            $message = 'There has been a problem updating the email address.  Error: ' . $e->getMessage();
            $this->orderService->logInteraction($message);
            $this->symfonyStyle->error($message);
            return Cli::RETURN_FAILURE;
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param $key
     * @param $prompt
     * @return mixed|string
     * @throws Exception
     */
    protected function updateBy($key, $prompt)
    {
        $question = $this->addNotEmptyValidator($prompt);
        $response = $this->symfonyStyle->ask("<question>$question</question>");

        $this->input->setOption(
            $key,
            $response
        );

        $errors = $this->validate();
        if ($errors) {
            $errorStr = implode('</error>' . PHP_EOL . '<error>', $errors);
            $this->output->writeln(__('<error>' . $errorStr . '</error>'));

            throw new Exception(__($errorStr));
        }

        return $response;
    }

    /**
     * @param $question
     * @return mixed
     * @throws \Exception
     */
    private function addNotEmptyValidator($question)
    {
        $question = trim($question);
        if ($question == '') {
            throw new \Exception('The value cannot be empty');
        }

        return $question;
    }

    /**
     * Get list of arguments for the command
     *
     * @param int $mode The mode of options.
     * @return InputOption[]
     */
    public function getOptionsList($mode = InputOption::VALUE_REQUIRED)
    {
        $requiredStr = ($mode === InputOption::VALUE_REQUIRED ? '(Required) ' : '');

        return [
            new InputOption(
                self::KEY_ORDER_ID,
                null,
                $mode,
                $requiredStr . 'Please enter order_id'
            ),
            new InputOption(
                self::KEY_EMAIL,
                null,
                $mode,
                $requiredStr . 'Please enter email address'
            ),
        ];
    }

    /**
     * Check if all admin options are provided
     *
     * @return string[]
     */
    public function validate()
    {
        $errors = [];

        $validator = new DataObject();
        $this->userValidationRules->addUserInfoRules($validator);

        if ($this->input->getOption(self::KEY_EMAIL)) {
            $user = new FrameworkDataObject();
            $user->setEmail($this->input->getOption(self::KEY_EMAIL));

            if (!$validator->isValid($user)) {
                $errors = array_merge($errors, $validator->getMessages());
            }
        }

        return $errors;
    }

    /**
     * @param $order
     * @param null $defaultEmail
     * @return mixed
     * @throws \Exception
     */
    protected function updateOrderEmail($order, $defaultEmail = null)
    {
        $currentEmail = $order->getCustomerEmail();
        $message = __('<info>Current order has id: %1 and customer email: %2 </info>', $order->getId(), $currentEmail);
        $this->symfonyStyle->writeln($message);
        $this->orderService->logInteraction($message);
        $newEmail = $defaultEmail ?: $this->symfonyStyle->ask(__("Please enter an new customer email", $defaultEmail));
        if ($currentEmail === $newEmail) {
            $message = 'Current email is the same as the new email address.';
            $this->symfonyStyle->writeln($message);
            $this->orderService->logInteraction($message, 'error');
            throw new InputValidationException($message);
        }
        $message = __(
            '<info>You are about to update order with id: %1 from current email: %2 to %3.</info>',
            $order->getId(),
            $currentEmail,
            $newEmail
        );
        $this->symfonyStyle->writeln($message);
        $this->orderService->logInteraction($message);
        $response = $this->symfonyStyle->confirm("Are you sure?[y/N]");
        if ($response == true) {
            $response = $this->orderService->setNewEmail($order, $currentEmail, $newEmail);
            $this->symfonyStyle->success($response);
        } else {
            $message = 'Current order email update has been canceled.';
            $this->orderService->logInteraction($message);
            throw new \Exception($message);
        }
        return $newEmail;
    }

    /**
     * @param array $orders
     * @throws \Exception
     */
    protected function selectOrderToUpdate(array $orders): int
    {
        // Replace Prefix with Order URL
        $prefix = self::SELECT_ORDER_ID_PREFIX;
        foreach ($orders as $key => $order) {
            $select[$key] = __("$prefix%1", $key);
        }
        $select[self::OPTION_ALL] = __(self::ALL_ORDERS);
        $select[self::OPTION_QUIT] = __(self::OPTION_QUIT_PROMPT);
        if ($orders) {
            $response = $this->symfonyStyle->choice("Please select from the list of order ids to update", $select);
            $this->orderService->logInteraction('Order id to update :' . $response);
            if ($response == self::OPTION_QUIT) {
                $this->quit = true;
                unset($orders[self::OPTION_QUIT]);
            } elseif ($response == self::OPTION_ALL) {
                unset($orders[self::OPTION_ALL]);
                $newEmail = null;
                foreach ($orders as $order) {
                    $newEmail = $this->updateOrderEmail($order, $newEmail);
                    unset($orders[$order->getId()]);
                }
            } else {
                $orderId = str_replace($prefix, '', $response);
                $order = $this->orderService->getOrder($response);
                $this->updateOrderEmail($order);
                unset($orders[$orderId]);
                $this->selectOrderToUpdate($orders);
            }
        }

        return CLI::RETURN_SUCCESS;
    }

    /**
     * @return OrderInterface[]
     * @throws Exception
     */
    protected function updateByEmail()
    {
        $this->symfonyStyle->title(self::OPTION_BY_EMAIL);
        $key = self::KEY_EMAIL;
        $prompt = self::OPTION_BY_EMAIL_PROMPT;
        $this->orderService->logInteraction($prompt);

        $emailAddress = $this->updateBy($key, $prompt);
        $orders = $this->orderService->searchByEmail($emailAddress);
        if (!$orders) {
            $message = sprintf('No orders with email %s exists!', $emailAddress);
            $this->orderService->logInteraction($message, 'error');

            throw new InputValidationException($message);
        }
        return $orders;
    }

    /**
     * @return OrderInterface
     * @throws Exception
     */
    protected function updateByOrderId()
    {
        $this->symfonyStyle->title(self::OPTION_BY_ORDER_ID);
        $key = self::KEY_ORDER_ID;
        $prompt = self::OPTION_BY_ORDER_ID_PROMPT;
        $response = $this->updateBy($key, $prompt);
        $this->orderService->logInteraction($prompt . ' response : ' . $response);
        return $this->orderService->getOrder($response);
    }

    /**
     * @return mixed
     */
    protected function topLevelQuestion()
    {
        $this->symfonyStyle->title(self::TITLE);
        $mainSelection = self::OPTION_PROMPT;
        $choices = [self::OPTION_BY_ORDER_ID, self::OPTION_BY_EMAIL, self::OPTION_QUIT];
        $defaultChoice = self::OPTION_BY_EMAIL;
        $this->orderService->logInteraction($mainSelection);

        return $this->symfonyStyle->choice($mainSelection, $choices, $defaultChoice);
    }

    /**
     * @return string
     */
    protected function getHelpDescription()
    {
        return <<<EOF
  Order Update Email Command Help
  _____________________________________

The <info>%command.name%</info> command is used to update an existing order's email via cli.

Example usage:
  <info>php %command.full_name%</info>
  <info>php bin/magento update-email</info>

EOF;
    }
}

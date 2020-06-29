<?php declare(strict_types=1);

namespace SajidPatel\OrderEmail\Model;

use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\EmailAddress;
use Zend_Validate_EmailAddress;
use Zend_Validate_Exception;

/**
 * Class for adding validation rules to an Admin user
 *
 * @api
 * @since 100.0.2
 */
class UserValidationRules
{
    /**
     * Adds validation rule for user first name, last name, username and email
     *
     * @param DataObject $validator
     * @return DataObject
     * @throws Zend_Validate_Exception
     */
    public function addUserInfoRules(DataObject $validator)
    {
        $emailValidity = new EmailAddress();
        $emailValidity->setMessage(
            __('Please enter a valid email.'),
            Zend_Validate_EmailAddress::INVALID
        );

        $validator->addRule(
            $emailValidity,
            'email'
        );

        return $validator;
    }
}

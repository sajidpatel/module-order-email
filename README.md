# Magento Module SalesOrder Coding Challenge

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [How to use it](#markdown-header-how-to-use-it)

## Main Functionalities

* Created a console command to update order email
1. Cli command should prompt user to enter order_id or Email or quit
2. Cli command should give a list of orders with minimum data to select from
3. Cli command should prompt the admin user to select one or all orders from the list
4. Cli command should prompt the user to enter the old email
5. Cli command should prompt the user to enter the new email
6. Cli command should prompt the user to confirm each order update (is this correct)
7. Cli command should update the order email of given order
8. Cli command should return the prompt to line 2

Assumption:
There are no security checks implemented, as anyone who has access to cli:
Can create/update admin credentials.


-----
## SalesOrder order email update Coding Challenge

The purpose of this Magento 2 module to as a coding challenge for suitable candidates.

## The Challenge

Will be emailed to candidates in due course.

-----

## Instalation 

### Type 1: Zip file

 - Unzip the zip file in to `app/code/SajidPatel/SalesOrder`
 - Enable the module by running `php bin/magento module:enable SajidPatel_SalesOrder`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Add repository vcs to composer
 ```
composer config repositories.sajidpatel.sales-order vcs
```
 - Install the module composer by running ```composer require sajidpatel/sales-order```
 - enable the module by running `php bin/magento module:enable SajidPatel_SalesOrder`
 - apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

Fork the repository as a public repository in your own github account
Complete the coding challenge
Submit your git repo to sajid.patel@galtone.co.uk


## How to use it 
Please select an order_id or email address

*Licence:* BSD-3-Clause  
*Author:* Sajid Patel 
*Copyright:* 2020 Sajid Patel 
*Website:* [http://sajidpatel.me/](http://sajidpatel.me/)  

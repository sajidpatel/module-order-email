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

 - Unzip the zip file in to `app/code/SajidPatel/OrderEmail`
 - Enable the module by running `php bin/magento module:enable SajidPatel_OrderEmail`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Add repository vcs to composer
 ```
composer config repositories.sajidpatel.module-order-email vcs
```
 - Install the module composer by running ```composer require sajidpatel/module-order-email```
 - enable the module by running `php bin/magento module:enable SajidPatel_OrderEmail`
 - apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

Fork the repository as a public repository in your own github account.
Complete the coding challenge.


## How to use it 
In the root directory on the command line enter:
```bin/magento ruroc:order:update-email```
or 
```bin/magento update-email```

Select an option from the main menu
```
Order Email Update
==================

 Please choose an update option. [Update by email address]:
  [0] Update by order ID
  [1] Update by email address
  [2] quit
 >
```
Select Option 0 to update by order id and enter an order id
```
Update by order ID
==================

 Please enter an Order ID:
 >1
```
Enter an email and confirm you are happy to proceed.
```
Current order has id: 1 and customer email: not_my_email@gmail.com

 Please enter a new customer email:
 > my_email@gmail.com

You are about to update order with id: 1 from current email: sammi_dws@yahoo.com to sammi_dws@hotmail.com.

 Are you sure?[y/N] (yes/no) [yes]:
 > y

 [OK] Order email address has changed for order: 1 from not_my_email@gmail.com to
      my_email@gmail.com
```

Select option 1 or simply press enter to search for orders by email
```
Order Email Update
==================

 Please choose an update option. [Update by email address]:
  [0] Update by order ID
  [1] Update by email address
  [2] quit
 >1

Update by email address
=======================

 Please enter current email address:
 > not_my_email@gmail.com

```
Select an order by id or select all or select quit
```
 Please select from the list of order ids to update:
  [2   ] Order id: 2
  [3   ] Order id: 3
  [4   ] Order id: 4
  [all ] Update All Orders
  [quit] Quit current process
 >all
```
Enter a new email and confirm for each order
```
Current order has id: 2 and customer email: not_my_email@gmail.com

 Please enter an new customer email:
 > my_email@gmail.com

You are about to update order with id: 2 from current email: not_my_email@gmail.com to my_email@gmail.com.

 Are you sure?[y/N] (yes/no) [yes]:
 > y

 [OK] Order email address has changed for order: 2 from not_my_email@gmail.com to
      my_email@gmail.com


Current order has id: 3 and customer email: not_my_email@gmail.com
You are about to update order with id: 3 from current email: not_my_email@gmail.com to my_email@gmail.com.

 Are you sure?[y/N] (yes/no) [yes]:
 >y

 [OK] Order email address has changed for order: 3 from not_my_email@gmail.com to
      my_email@gmail.com

Current order has id: 4 and customer email: not_my_email@gmail.com
You are about to update order with id: 4 from current email: not_my_email@gmail.com to my_email@gmail.com.

 Are you sure?[y/N] (yes/no) [yes]:
 >y

 [OK] Order email address has changed for order: 4 from not_my_email@gmail.com to
      my_email@gmail.com

```
*License:* OSL-3.0<br>
*Author:* Sajid Patel<br>
*Copyright:* 2020 Sajid Patel<br>
*Website:* [http://sajidpatel.me/](http://sajidpatel.me/)  

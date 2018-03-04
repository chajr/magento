# FreshSales Account

## Basic description

### Create customer account

Current module allows to automatically create _FreshSales account_ for customer after he create account in our store.  
Directly after user is added into database in Magento store, there is created call for _FreshSales API_ in order to create
_Lead_ account. After that _FreshSales Lead ID_ is saved as _Magento customer attribute_.

FreshSales customer data send into FreshSales API:
* First name
* Last name
* Email
* Creation & update datetime

### Edit customer FreshSales ID

If after creating customer account there appear some exception, or some of customers was created before module
installation there is possibility to manually set up _FreshSales ID_ in Magento backend.  
To do this go into `Customers->ManagesCustomers->(customer)->Account Information` and add _FreshSales ID_ into input
called **Customer FreshSales ID**

## Module

### Setup module

After module installation we need to provide two required parameters _API key_ and _FreshSales user name_.  
Both of them are required to properly module working. _API key_ is special token value that allow module to connect
and authorize with _FreshSales API_. That value can be taken from `Settings->API SETTINGS` on _FreshSales Account_.
Second value _FreshSales user name_ is a _FreshSales main user name_ used ad sub-domain after login into
_FreshSales Account_.  
After that setup each new customer should be linked with _FreshSales Account_ after successful registration.

### Errors by creating account via API

During whole process there is some possibility that not everything go ok. In that case module can handle _FreshSales API_
and Magento exceptions and store it into log files.

Exception types
* **InvalidArgumentException** - Called if some data value is incorrect (like missing API key or Access Denied). Message for that exceptions are stored in `var/log/freshsales.log`
* **UnexpectedValueException** - Called if some unexpected value is returned (like Page not found). Message for that exceptions are stored in `var/log/freshsales.log`
* **RuntimeException** - That exception are called if there was some problems on _FreshSales API_ side, or data send into API was incorrect Message for that exceptions are stored in `var/log/freshsales.log`
* **Exception** - All other Exceptions. Message for that exceptions are stored in `var/log/exception.log`

`Exception` can be also type of `DomainException` and `OutOfRangeException`. First is called if some undefined error
was detected with API communication and second one whe to many requests was called to _FreshSales API_.  
Also that part is handling some Magento exceptions like Database exceptions.

### How it works

How module works from technical way?
When customer create account Magento trigger special event `customer_register_success`. When _FreshSales module_ detect
that event collect required user data nad send it into _FreshSales API_ using `curl` library.  
If everything was ok, then _FreshSales API_ will return response with ID that will be saved as special customer attribute.
That attribute is created in database during module installation process.

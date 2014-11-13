SEQR Prestashop plugin
======================

# SEQR #
SEQR is Sweden’s and Europe’s most used mobile wallet in stores and online. SEQR enables anybody with a smartphone to pay in stores online and in-app.
Users can also transfer money at no charge, store receipts digitally and receive offers and promotions directly through one mobile app.

SEQR offer the merchant 50% in reduction to payment card interchange and no capital investment requirements.
SEQR as method of payment is also completely independent of PCI and traditional card networks.

SEQR is based on Seamless’ technology, a mobile phone payment and transaction service using QR codes & NFC on the front-end and Seamless’ proven transaction server on the back-end.
SEQR is the only fully-integrated mobile phone payment solution handling the entire transaction chain, from customer through to settlement.
Through our state of the art technology, we have created the easiest, secure, and most cost effective payment system.

Learn more about SEQR on www.seqr.com

## Supported Prestashop versions: ##
* 1.6
* 1.5
* 1.4

## Dowloads ##
* Version 1.0: [seqr-ps-plugin-1.0.zip](build/seqr-ps-plugin-1.0.zip)
* all versions: [builds](build/)

# Plugin #
Plugin provide possibility for shop clients to select SEQR as payment method, and after order placement pay it via scanning QR code (or directly from your mobile device).

* SEQR as payment method on checkout page.

![alt tag](docs/payment_option.png)

* SEQR payment summary.

![alt tag](docs/payment_summary.png)

* Payment via scanning of QR code.

![alt tag](docs/payment_code.png)

* Payment confirmation

![alt tag](docs/payment_completed.png)

## Installation & Configuration ##

Plugin can be installed via installation in administration or by copping all plugin files to the "modules" directory.

### Installation using administration page ###

1. Please download a build package from: [builds](build/).
2. Open Prestashop administration page, go to "Modules" and on the top right corner choose "Add a new module".
3. Select the downloaded package and confirm by clicking "Upload this module".
4. Find the module on the module list and install it.
5. Provide valid configuration data.

### Configuration ###

![alt tag](docs/seqr_settings.png)

Plugin configuration properties are available on the module configuration page.

Contact Seamless on integrations@seamless.se to get the right settings for the SOAP url, Terminal ID and Terminal Password.

Default timeout is set to 120 seconds.

All properties are required and should be configured before enabling this payment method in production.

## Development & File structure ##

Plugin based on javascript plugin for SEQR integration.

Please check it for understanding how work web component http://github.com/SeamlessDistribution/seqr-webshop-plugin.
For more information about SEQR API please check http://developer.seqr.com/merchant/webshop/

### Plugin directories: ###
* controllers
* css
* img
* js
* lib
* views
* seqr.php

### Main php classes ###
* seqr/seqr.php - an entry point of the module, provides information about module, administration form, installation and remove module procedure.
* seqr/impl/prestashop/PsConfig.php - defines configuration for the Prestashop platform, installation, uninstall definitions.
* seqr/impl/prestashop/PsFactory.php - defines conversion from Prestashop order to the unified invoice representation.
* seqr/impl/prestashop/PsSeqrService.php - defines logic, sens request to the SEQR system via provided API (seqr/lib/api/SeqrApi.php)


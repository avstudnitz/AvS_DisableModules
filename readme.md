AvS_DisableModules
=====================
Adds a shell command `info:dependencies:show-removable` which exports all modules which have no dependencies 

Facts
-----
- version: 1.0.0
- [extension on GitHub](https://github.com/avstudnitz/AvS_DisableModules)
- [direct download link](https://github.com/avstudnitz/AvS_DisableModules/archive/master.tar.gz)
- Composer key: `avstudnitz/disable-modules` (registered at Packagist)

Description
-----------
Call the new command as follows: 

```
$ bin/magento info:dependencies:show-removable
```

You'll get an output as follows:

```
Report successfully processed. File "modules-removable.csv" generated.
```

The file "modules-removable.csv" will contain a list of modules: 

```
"Modules without dependencies:"
" =========================== "

Magento_Weee
Magento_WebapiSecurity
Magento_Version
Magento_Usps
Magento_Ups
Magento_TaxImportExport
Magento_SwatchesLayeredNavigation
Magento_Swagger
Magento_Sitemap
Magento_SendFriend
Magento_SalesInventory
Magento_ProductVideo
Magento_Persistent
Magento_OfflinePayments
Magento_NewRelicReporting
Magento_Multishipping
Magento_LayeredNavigation
Magento_GroupedImportExport
Magento_GoogleOptimizer
Magento_GoogleAdwords
Magento_Fedex
Magento_EncryptionKey
Magento_DownloadableImportExport
Magento_Dhl
Magento_Deploy
Magento_CustomerImportExport
Magento_CurrencySymbol
Magento_ConfigurableImportExport
Magento_CheckoutAgreements
Magento_CatalogWidget
Magento_CatalogRuleConfigurable
Magento_Captcha
Magento_CacheInvalidate
Magento_BundleImportExport
Magento_Braintree
Magento_Authorizenet
Magento_AdvancedPricingImportExport
Magento_AdminNotification
Magento_Marketplace
Magento_BundleSampleData
Magento_SalesRuleSampleData
Magento_SalesSampleData
Magento_CustomerSampleData
Magento_CmsSampleData
Magento_TaxSampleData
Magento_GroupedProductSampleData
Magento_DownloadableSampleData
Magento_WidgetSampleData
Magento_MsrpSampleData
Magento_WishlistSampleData
Magento_ReviewSampleData
Magento_SwatchesSampleData
Magento_OfflineShippingSampleData
AvS_DisableModules
```

No other modules have any dependencies declared to any of those modules, so they can be removed savely (in theory). 
To remove them, add `bin/magento module:disable ` in front of the module name you want to remove, i.e.:

```
bin/magento module:disable Magento_Marketplace
```


Requirements
------------
- PHP >= 5.6.0

Compatibility
-------------
- Magento  >= 2.1.0 (not tested on 2.0.x)

Installation Instructions
-------------------------
1. Install the extension via Composer with the key shown above or copy all the files into the newly created directory 
`app/code/AvS/DisableModules/` in the Magento 2 root.
2. Enable the extension by calling `bin/magento module:enable AvS_DisableModules`.
3. Run `bin/magento setup:upgrade`.

Uninstallation
--------------
1. Uninstall the extension by calling `bin/magento module:uninstall AvS_DisableModules`.
2. Remove all extension files from `app/code/AvS/DisableModules/` or use Composer to remove the extension if you have installed it with Composer


Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/avstudnitz/AvS_DisableModules/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Andreas von Studnitz, integer_net

[http://www.integer-net.com](http://www.integer-net.com)

[@avstudnitz](https://twitter.com/avstudnitz)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2017 Andreas von Studnitz / integer_net GmbH

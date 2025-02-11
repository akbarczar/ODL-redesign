# magento-layered-navigation

Overview: The Magento 2 Layered Navigaton extension allows the users to add an attractive
and extra fexible navigation to their store.
It also significantly expands default Magento 2 layered navigation fixes specific issues and adds
many new features.

List of features:

Improved Navigation:
Provides improved navigaton to the user’s online store.

Fast User Experience :
Customers can experience fast ajax search for products according to various filters at the product
list page as well as product search result page.
Customers can also experience the price slider at the product list page as well as the product
search result page.

Ajax Loadiog Support :
With this the products fltered by buyers instantly appear. As per the version of Layered
Navigation the fltered products can be shown just afer each filtering or afer all flters have been
applied. Thus this not just improves buyer’s experience but also speeds up the page loading
process.

Price Slider :
Allows users to flter products by a certain price range. You can determine the range of the product
as per your preferred price range.

Configuration Menu for Layered Navigation Module :

1. Admin can enable/disable the ajax in layered navigaton for various filters in the product list
page and product search result page.
2. Admin can enable/disable price slider in layered navigation in the product list page and product
search result page.

# Installation Instruction

* Copy the content of the repo to the Magento 2 app/code/Ksolves/LayeredNavigation 
* Run command:
<b>php bin/magento setup:upgrade</b>
* Run Command:
<b>php bin/magento setup:di:compile</b>
* Run Command:
<b>php bin/magento setup:static-content:deploy</b>
* Now Flush Cache: <b>php bin/magento cache:flush</b>

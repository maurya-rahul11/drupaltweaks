## This is installation guide -

1. Install endroid qr code php library using composer - https://packagist.org/packages/endroid/qr-code
  ```composer require endroid/qr-code```
2. Take Git Clone
  ```git clone git@github.com:maurya-rahul11/drupaltweaks.git```
3. Copy module to custom directory of any drupal project.
4. Install module product_qr module using drush or through UI
  ```drush en product_qr```
5. Upon installation a content type will be created with name 'Product' and view with name 'Product List'
6. On product detail page, QR Code is placed in block in Sidebar region.
7. Refer hosted site on Pantheon - https://dev-drupal-utils.pantheonsite.io
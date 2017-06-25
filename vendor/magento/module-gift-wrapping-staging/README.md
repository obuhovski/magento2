<h2>Magento_GiftWrappingStaging module</h2>

## Overview

The Magento_GiftWrappingStaging module is a part of the staging functionality in Magento EE. It allows to stage value of 'Allow Gift Wrapping' flag and price of the wrapping for each product update.

## Implementation Details

The Magento_GiftWrappingStaging module adds to the Schedule Update form of a product the following functionality:

- Enable/diable gift wrapping ("Allow Gift Wrapping" field)
- Set a price for the gift wrapping ("Price for Gift Wrapping" field).

## Dependencies

You can find the list of modules that have dependencies with the Magento_GiftWrappingStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

[Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_GiftWrappingStaging module.

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
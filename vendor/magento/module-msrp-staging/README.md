<h2>Magento_MsrpStaging module</h2>

## Overview

The Magento_MsrpStaging module is a part of the staging functionality in Magento EE. It enables you to stage the manufacturer's suggested retail price.

## Implementation Details

The Magento_MsrpStaging module extends the Magento_Msrp module to be used in staging. It adds the following fields in the Advice Pricing form:

- Manufacturer's Suggested Retail Price
- Display Actual Price

## Dependencies

You can find the list of modules that have dependencies with the Magento_MsrpStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

[Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_MsrpStaging module.

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.1/release-notes/changes/ee_changes.html).
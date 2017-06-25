<h2>Magento RmaStaging module</h2>

## Overview

The Magento_RmaStaging module is a part of the staging functionality in Magento EE. It enables you to create updates for the parameters of Autosettings field set of a product.

RMA stands for a return merchandise authorization.

## Implementation Details

The Magento_RmaStaging module extends the following Magento_Rma module functionality to be used in staging mode:

- Adds Autosettings field set to the Schedule update form of a product.

## Dependencies

You can find the list of modules that have dependencies with the Magento_RmaStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

Extension points enable extension developers to interact with the Magento_RmaStaging module. [Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_RmaStaging module.

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.1/release-notes/changes/ee_changes.html).
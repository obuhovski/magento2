<h2>Magento CatalogRuleStaging module</h2>

## Overview

The Magento_CatalogRuleStaging module is a part of the staging functionality in Magento EE. It enables you to add the catalog rule updates to existing store campaigns. In other words, you can add and/or remove catalog rules for some period of time. These updates are shown on the campaign dashboard.

## Implementation Details

The Magento_CatalogRuleStaging module changes a catalog rule creation page and the catalog rule related database tables to make them compatible with the Magento Staging Framework. This module depends on the Magento_CatalogRule module and extends its functionality. It changes database structure of the Magento_CatalogRule module and the way in which catalog rules are managed. The Magento_CatalogRule module must be enabled.

The Magento_CatalogRuleStaging module enables you to stage the following catalog rule attributes:

- Rule Name
- Description
- Websites
- Customer Groups
- Priority
- Product Apply
- Product Discount Amount
- Subproduct Discounts
- Subproduct Apply
- Subproduct Discount Amount
- Discard Subsequent Rules

These attributes cannot be modified and are a part of the static Magento Catalog Rule form.

### Installation Details

The Magento_CatalogRuleStaging module makes irreversible changes in a database during installation. It means, that you cannot uninstall this module.

## Dependencies

You can find the list of modules that have dependencies with the Magento_CatalogRuleStaging module in the `require` object of the `composer.json` file. The file is located in the same directory as this `README` file.

## Extension Points

Extension points enable extension developers to interact with the Magento_CatalogRuleStaging module. You can interact with the Magento_CatalogRuleStaging module using the Magento extension mechanism, see [Magento plug-ins](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/plugins.html).

[Magento dependency injection mechanism](http://devdocs.magento.com/guides/v2.1/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Magento_CatalogRuleStaging module.

### Layouts

You can extend and override layouts in the `app/code/Magento/CatalogRuleStaging/view/adminhtml/layout` directory.
For more information about layouts, see the [Layout documentation](http://devdocs.magento.com/guides/v2.1/frontend-dev-guide/layouts/layout-overview.html).

## Additional Information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
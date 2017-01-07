<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\web\twig\variables;

use Craft;

/**
 * Settings functions.
 *
 * @author     Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since      3.0
 * @deprecated in 3.0
 */
class SystemSettings
{
    // Public Methods
    // =========================================================================

    /** @noinspection MagicMethodsValidityInspection */
    /**
     * Returns whether a setting category exists.
     *
     * @param string $category
     *
     * @return bool
     */
    public function __isset(string $category)
    {
        return true;
    }

    /** @noinspection MagicMethodsValidityInspection */
    /**
     * Returns the system settings for a category.
     *
     * @param string $category
     *
     * @return array
     */
    public function __get(string $category)
    {
        Craft::$app->getDeprecator()->log('craft.systemSettings.[category]', 'craft.systemSettings.[category] has been deprecated. Use craft.app.systemSettings.getSettings(\'category\') instead.');

        return Craft::$app->getSystemSettings()->getSettings($category);
    }
}

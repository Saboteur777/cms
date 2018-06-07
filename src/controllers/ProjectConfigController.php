<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\controllers;

use Craft;
use craft\models\UserGroup;
use craft\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * The ProjectConfigController class is a controller that handles various project config tasks such as
 * re-generating configuration files and applying pending changes.
 * Note that all actions in this controller require admin access and an elevated session in order to execute.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.1
 */
class ProjectConfigController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // All user settings actions require an admin
        $this->requireAdmin();
        $this->requireElevatedSession();
        $this->requirePostRequest();

        return parent::beforeAction($action);
    }

    /**
     * Regnerate snapshot from current YAML configuration files.
     * This will also update the snapshot.
     *
     * @return Response
     * @throws NotFoundHttpException if the requested user group cannot be found
     */
    public function actionRegenerateSnapshot(): Response
    {
        $configService = Craft::$app->getProjectConfig();
        $configService->updateSnapshot();
        $configService->updateDateModifiedCache();

        return $this->redirectToPostedUrl();
    }

    /**
     * Regenerate the config from current site snapshot and save the changes the YAML configuration.
     * This will also update the snapshot.
     *
     * @return Response
     * @throws NotFoundHttpException if the requested user group cannot be found
     */
    public function actionRegenerateConfig(): Response
    {
        Craft::$app->getProjectConfig()->generateConfigFileFromSnapshot();

        return $this->redirectToPostedUrl();
    }

    /**
     * Regnerate a config from current site structure and save the changes the YAML configuration.
     * This will also update the snapshot.
     *
     * @return Response|null
     * @throws NotFoundHttpException if the requested user group cannot be found
     */
    public function actionRegenerateConfigMappings(): Response
    {
        $configService = Craft::$app->getProjectConfig();

        if ($configService->updateConfigMap()) {
            $configService->updateDateModifiedCache();
        }

        return $this->redirectToPostedUrl();
    }
}

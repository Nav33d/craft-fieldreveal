<?php

/**
 * Field Reveal plugin for Craft CMS 3
 *
 * View where your custom fields are being used.
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\fieldreveal\controllers;

use Craft;
use craft\db\Query;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\ForbiddenHttpException;
use yii\web\Response;

use nav33d\fieldreveal\assetbundles\FieldRevealAsset;
use nav33d\fieldreveal\FieldReveal as FieldRevealPlugin;

class DefaultController extends Controller
{

    /**
     * Show the dashboard
     * 
     * @return Response
     */
    public function actionDashboard(): Response
    {
        $this->view->registerAssetBundle(FieldRevealAsset::class);

        $templateTitle = Craft::t('fieldreveal', 'Dashboard');

        $variables['title'] = $templateTitle;
        $variables['crumbs'] = [
            [
                'label' => 'Field Reveal',
                'url' => UrlHelper::cpUrl('fieldreveal'),
            ],
        ];

        $variables['baseAssetsUrl'] = Craft::$app->assetManager->getPublishedUrl(
            '@nav33d/fieldreveal/assetbundles/dist',
            true
        );

        return $this->renderTemplate('fieldreveal/dashboard/_index', $variables);
    }


    /**
     * Show field reveal index page
     * 
     * @return Response
     */
    public function actionIndex()
    {
        $this->view->registerAssetBundle(FieldRevealAsset::class);

        $templateTitle = Craft::t('fieldreveal', 'Field Reveal');

        $variables['title'] = $templateTitle;
       
        $variables['baseAssetsUrl'] = Craft::$app->assetManager->getPublishedUrl(
            '@nav33d/fieldreveal/assetbundles/dist',
            true
        );

        return $this->renderTemplate('fieldreveal/fields/_index', $variables);
    }


    /**
     * Get all fields
     * 
     * @param string $fieldName
     * @param string $fieldGroup
     * 
     * @return Response
     */
    public function actionGetFields($fieldName = "", $fieldGroup = "") : Response
    {
        $data['fields'] = FieldRevealPlugin::$plugin->fieldreveal->getFields($fieldName, $fieldGroup); 
        $data['unusedFieldIds'] = FieldRevealPlugin::$plugin->fieldreveal->getUnusedFieldIds();
        $data['fieldGroups'] = FieldRevealPlugin::$plugin->fieldreveal->getFieldGroups();   

        return $this->asJson($data);
    }


    /**
     * Return field data
     * 
     * @param int $fieldId
     * 
     * @return Response
     */
    public function actionGetFieldData($fieldId): Response
    {
        $data = FieldRevealPlugin::$plugin->fieldreveal->getFieldData($fieldId);
        return $this->asJson($data);
    }


    /**
     * Delete a field
     * 
     * @param int $fieldId
     * 
     * @return Response
     */
    public function actionDeleteField(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $fieldId = $request->getRequiredBodyParam('fieldId');

        $response = FieldRevealPlugin::$plugin->fieldreveal->deleteField($fieldId);
        return $this->asJson(['success' => $response]);
    }

}

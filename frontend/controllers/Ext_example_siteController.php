<?php defined('MW_PATH') || exit('No direct script access allowed');

Yii::import('frontend.controllers.SiteController');

class Ext_example_siteController extends SiteController
{
    // the extension instance
    public $extension;

    // move the view path
    public function getViewPath()
    {
        return Yii::getPathOfAlias('ext-example.frontend.views.site');
    }

    /**
     * Common settings
     */
    public function actionIndex()
    {
        $this->render('index');
    }
}

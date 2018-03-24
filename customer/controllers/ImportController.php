<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Controller file for settings.
 */

class ImportController extends Controller
{
    // the extension instance
    public $extension;

    // move the view path
    public function getViewPath()
    {
        return Yii::getPathOfAlias('ext-import.customer.views.index');
    }

    /**
     * Common settings
     */
    public function actionIndex()
    {
        echo "112313131312312313131231231332q241234";
        die;

        $import  = new ListCsvImport('upload');

        $this->setData(array(
            'pageMetaTitle'    => $this->data->pageMetaTitle . ' | '. $this->extension->t('import'),
            'pageHeading'      => $this->extension->t('import'),
            'pageBreadcrumbs'  => array(
                Yii::t('app', 'Extensions') => $this->createUrl('multiple/import'),
                $this->extension->t('Example') => $this->createUrl('multiple/import'),
            )
        ));

        $this->render('index',compact('import'));
    }

}

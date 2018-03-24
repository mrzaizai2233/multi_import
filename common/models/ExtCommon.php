<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ExampleExtCommon
 *
 */

class ExtCommon extends FormModel
{
    public $enabled = 'no';

    public $dummy_setting = '';

    public function rules()
    {
        $rules = array(
            array('dummy_setting', 'safe'),
            array('enabled', 'in', 'range' => array_keys($this->getYesNoOptions())),
        );
        return CMap::mergeArray($rules, parent::rules());
    }

    public function attributeLabels()
    {
        $labels = array(
            'enabled'       => Yii::t('app', 'Enabled'),
            'dummy_setting' => $this->getExtensionInstance()->t('Number char compare'),
        );
        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    public function attributePlaceholders()
    {
        $placeholders = array(
            'dummy_setting' => 'Number char compare',
        );
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'enabled'       => Yii::t('app', 'Whether the feature is enabled'),
            'dummy_setting' => $this->getExtensionInstance()->t('Number char compare'),
        );
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    public function save()
    {
        $extension  = $this->getExtensionInstance();
        $attributes = array('enabled', 'dummy_setting');
        foreach ($attributes as $name) {
            $extension->setOption($name, $this->$name);
        }
        return $this;
    }

    public function populate()
    {
        $extension  = $this->getExtensionInstance();
        $attributes = array('enabled', 'dummy_setting');
        foreach ($attributes as $name) {
            $this->$name = $extension->getOption($name, $this->$name);
        }
        return $this;
    }

    public function getExtensionInstance()
    {
        return Yii::app()->extensionsManager->getExtensionInstance('example');
    }

    public function getYesNoOptions()
    {
        return array(
            self::TEXT_YES  => ucfirst(Yii::t('app', self::TEXT_YES)),
            self::TEXT_NO   => ucfirst(Yii::t('app', self::TEXT_NO)),
        );
    }
}

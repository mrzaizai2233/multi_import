<?php
$form = $this->beginWidget('CActiveForm', array(
    'action'        => array('multiple/import//import'),
    'htmlOptions'   => array(
        'id'        => 'upload-csv-form',
        'enctype'   => 'multipart/form-data',
        'm'
    ),
));
?>
<div class="box box-primary borderless">
    <div class="box-header">
        <div class="pull-left">
            <h3 class="box-title"><i class="glyphicon glyphicon-list-alt" data-original-title="" title=""></i><?php echo Yii::t('app', 'Import Extension') ?></h3>            </div>
        <div class="pull-right">
        </div>
        <div class="clearfix"><!-- --></div>
    </div>
    <div class="box-body">
        <div class="form-group">
            <?php echo $form->labelEx($import, 'file');?>
            <?php echo CHtml::activeFileField($import, 'files[]', array('multiple' => true,'class'=>'form-control')); ?>
            <?php echo $form->error($import, 'file');?>
        </div>
    </div>
    <div class="box-footer">
        <div class="pull-right">
            <button type="button" class="btn btn-primary btn-flat" onclick="$('#upload-csv-form').submit();"><?php echo Yii::t('app', 'Next')?></button>
        </div>

    </div>
</div>


<?php $this->endWidget(); ?>


<?php
$form = $this->beginWidget('CActiveForm', array(
    'action'        => array('multiple_import/import'),
    'htmlOptions'   => array(
        'id'        => 'upload-csv-form',
        'enctype'   => 'multipart/form-data',
        'm'
    ),
));
?>

    <div class="form-group">
        <?php echo $form->labelEx($import, 'file');?>
        <?php echo CHtml::activeFileField($import, 'files[]', array('multiple' => true,'class'=>'form-control')); ?>
        <?php echo $form->error($import, 'file');?>
    </div>
    <button type="button" class="btn btn-primary btn-flat" onclick="$('#upload-csv-form').submit();"><?php echo Yii::t('list_import', 'Upload file')?></button>

<?php $this->endWidget(); ?>


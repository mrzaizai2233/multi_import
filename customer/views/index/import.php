<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package MailWizz EMA
 * @author Serban George Cristian <cristian.serban@mailwizz.com>
 * @link http://www.mailwizz.com/
 * @copyright 2013-2018 MailWizz EMA (http://www.mailwizz.com)
 * @license http://www.mailwizz.com/license/
 * @since 1.0
 */

?>

<div class="callout callout-info">
    <?php
    $text = 'The import process will start shortly. <br />
    While the import is running it is recommended you leave this page as it is and wait for the import to finish.<br />
    The importer runs in batches of {subscribersPerBatch} subscribers with a pause of {pause} seconds between the batches, therefore 
    the import process might take a while depending on your file size and number of subscribers to import.<br />
    Please note, the subscribers number is aproximate if your CSV has empty lines or ends with an empty line or it has duplicate emails(case in which the subscriber will be updated).<br />
    This is a tedious process, so sit tight and wait for it to finish.';
    echo Yii::t('list_import', StringHelper::normalizeTranslationString($text), array(
        '{subscribersPerBatch}' => $importAtOnce,
        '{pause}' => $pause,
    ));
    ?>
</div>

<div class="box box-primary borderless">
    <div class="box-header">
        <div class="pull-left">
            <h3 class="box-title">
                <?php echo IconHelper::make('import') . Yii::t('list_import', 'CSV import progress');?>
            </h3>
        </div>
        <div class="pull-right">
            <?php echo CHtml::link(IconHelper::make('back') . Yii::t('list_import', 'Back to import options'), array('list_import/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-primary btn-flat', 'title' => Yii::t('app', 'Back')));?>
        </div>
        <div class="clearfix"><!-- --></div>
    </div>
    <div class="box-body" id="csv-import" data-files='<?php echo CJSON::encode($file_uploaded) ?>' data-model="<?php echo $import->modelName;?>" data-pause="<?php echo (int)$pause;?>" data-iframe="<?php echo $this->createUrl('list_import/ping');?>" data-attributes='<?php echo CJSON::encode($import->attributes);?>'>
        <span class="counters">
            <?php echo Yii::t('list_import', 'From a total of {total} subscribers, so far {totalProcessed} have been processed, {successfullyProcessed} successfully and {errorProcessing} with errors. {percentage} completed. File imported {files}', array(
                '{total}' => '<span class="total">0</span>',
                '{totalProcessed}' => '<span class="total-processed">0</span>',
                '{successfullyProcessed}' => '<span class="success">0</span>',
                '{errorProcessing}' => '<span class="error">0</span>',
                '{percentage}'  => '<span class="percentage">0%</span>',
                '{files}'  => '<b><span class="files">0/0</span></b>',
            ));?>
        </span>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only">0% <?php echo Yii::t('app', 'Complete');?></span>
            </div>
        </div>
        <div class="alert alert-info log-info">
            <?php echo Yii::t('list_import', 'The import process is starting, please wait...');?>
        </div>
        <div class="log-errors"></div>
    </div>
    <div class="box-footer"></div>
</div>

<script>
    jQuery(document).ready(function($){

        // IMPORTER
        function importer($elem) {
            if (!$elem.length) {
                return;
            }

            var rowCount = 0,
                timeout = 1,
                haltExecution = false,
                importSuccessCount = 0,
                importErrorCount = 0,
                importCount = 0,
                recordsCount = -1,
                recordsIteration = 0,
                percentage = 0;

            var $importSuccessCount = $elem.find('.counters .success'),
                $importErrorCount = $elem.find('.counters .error'),
                $importTotalProcessed = $elem.find('.counters .total-processed'),
                $importTotal = $elem.find('.counters .total'),
                $importPercentage = $elem.find('.counters .percentage'),
                $importFiles = $elem.find('.counters .files'),

                $logInfo = $elem.find('.log-info'),
                $logErrors = $elem.find('.log-errors'),
                $progress = $elem.find('.progress').eq(0),
                $progressBar = $progress.find('.progress-bar'),
                $progressBarSr = $progressBar.find('.sr-only'),
                pause = $elem.data('pause') * 1000;

            function doQueueMessage(messageObject, counter, doHaltExecution) {
                setTimeout(function(){
//                    if (haltExecution) {
//                        return;
//                    }

                    if (messageObject.type == 'error') {
                        messageObject.type = 'danger';
                    }

                    $logInfo.html(messageObject.message);
                    if (messageObject.type == 'danger') {
                        $logErrors.prepend('<div class="alert alert-'+messageObject.type+'">'+messageObject.message+'</div>');
                    }
                    rowCount--;

                    if (messageObject.counter) {

                        if (messageObject.type == 'success' || messageObject.type == 'info') {
                            importSuccessCount++;
                        } else if (messageObject.counter && messageObject.type == 'danger') {
                            importErrorCount++;
                        }

                        importCount = importSuccessCount + importErrorCount;
                        $importTotalProcessed.html(importCount);
                        $importSuccessCount.html(importSuccessCount);
                        $importErrorCount.html(importErrorCount);

                        recordsIteration++;
                        percentage = Math.floor((recordsIteration / recordsCount) * 100);
                        $progressBar.width(percentage + '%');
                        $progressBarSr.html(percentage + '%');
                        $importPercentage.html(percentage + '%');
                        $importTotal.html(importCount);
                    }

                    haltExecution = (doHaltExecution === true ? true : false);
                }, counter * timeout);
            }
            var count = 1;

            function sendRequest(attributes) {


                attributes = attributes || $elem.data('attributes');
                var files = $elem.data('files');
                var modelName = $elem.data('model');
                var sendData = {};
                sendData[modelName] = {};
                var imported=0;
                var total_files =0;

                for(i in files){
                    if(files[i]==''){
                        imported+=1;
                    }
                    total_files +=1;
                }
                $importFiles.html(imported+'/'+total_files)

                for (i in attributes) {
                    sendData[modelName][i] = attributes[i];
                }
                if(count==1){
                    for (i in files){
                        if(files[i]!=''){
                            sendData[modelName].name = files[i];
                            sendData[modelName]['file_name'] = i;
                            files[i]='';
                            $elem.data('file',files);
                            count=2;
                            break;
                        }

                    }
                }
                if(sendData[modelName].name ==''){
                    for (i in files){
                        if(files[i]!=''){
                            sendData[modelName].name = files[i];
                            sendData[modelName]['file_name'] = i;
                            files[i]='';
                            $elem.data('file',files);
                            break;
                        }
                    }
                }


                // console.log(sendData)
                if ($('meta[name=csrf-token-name]').length && $('meta[name=csrf-token-value]').length){
                    var csrfTokenName = $('meta[name=csrf-token-name]').attr('content'),
                        csrfTokenValue = $('meta[name=csrf-token-value]').attr('content');
                    sendData[csrfTokenName] = csrfTokenValue;
                }

//                console.log(files)
                var name = sendData[modelName].name;
                console.log(name)
                if(sendData[modelName].name==''){
                        return;
                }

                $.ajax({
                    url: '',
                    data: sendData,
                    type: 'POST',
                    dataType: 'json'
                }).done(function(json){
                    if (json.result == 'error') {
                        json.attributes = {
                            rows_count:0,
                            current_page:1,
                            is_first_batch:1,
                            name:'',
                            file_name:''
                        };

                        sendRequest(json.attributes);
                        doQueueMessage({type:'error', message: json.message, counter: false}, 1, true);

                    } else if (json.result == 'success'){
                        if (json.attributes) {
                            json.attributes.name = name;
                            setTimeout(function(){
                                sendRequest(json.attributes);
                            }, pause);
                        } else {
                            setTimeout(function(){
                                json.attributes = {
                                    rows_count:0,
                                    current_page:1,
                                    is_first_batch:1,
                                    name:'',
                                    file_name:''
                                };
                                sendRequest(json.attributes);

                            }, pause);
                        }

                        if (json.recordsCount && recordsCount == -1) {
                            recordsCount = json.recordsCount;
                        }

                        if (json.import_log) {
                            for (i in json.import_log) {
                                rowCount++;
                                doQueueMessage(json.import_log[i], rowCount);
                            }
                        }

                        rowCount++;
                        doQueueMessage({type:'success', message: json.message, counter: false}, rowCount);
                    }
                }).fail(function(jqXHR){
                    if (jqXHR.statusText == 'error') {
                        jqXHR.statusText = 'Error, aborting the import process!'
                    }
                    doQueueMessage({type:'error', message: jqXHR.statusText, counter: false}, 1, true);
                });
            }
            sendRequest();

            // fake iframe to avoid cookie expiration.
            setInterval(function() {
                var iframe = $('<iframe/>', {
                    src: $('#list-import-log-container').data('iframe'),
                    width: 1,
                    height: 1
                }).css({display:'none'});
                $('body').append(iframe);
                setTimeout(function(){
                    iframe.remove();
                }, 1000 * 60 * 2);
            }, 1000 * 60 * 20);
        };

        // START IT
        importer($('#csv-import'));
        // ping page from within iframe
        (function(){
            if (!$('#ping').length || !window.opener) {
                return;
            }
            if ($('meta[name=csrf-token-name]').length && $('meta[name=csrf-token-value]').length){
                var csrfTokenName = $('meta[name=csrf-token-name]').attr('content'),
                    csrfTokenValue = $('meta[name=csrf-token-value]').attr('content');

                window.opener.$('meta[name=csrf-token-name]').attr('content', csrfTokenName);
                window.opener.$('meta[name=csrf-token-value]').attr('content', csrfTokenValue);
            }
        })();

        if ($('#database-import-modal .has-help-text').length) {
            $('#database-import-modal .has-help-text').on('shown.bs.popover', function(){
                $('.popover').css({zIndex: 99999});
            });
        }
    });
</script>
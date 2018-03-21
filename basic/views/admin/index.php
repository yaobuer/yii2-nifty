<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Admin;

$this->title = Yii::t('app/menu', 'administrator');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row pad-btm">
    <div class="col-sm-9 toolbar-left">
    	<?php $form = ActiveForm::begin([
			'action' => Url::to(['/admin/index']),
			'method' => 'get',
			'options'=>['class' => 'form-inline search-inline'],
			'enableClientValidation' => false,
		]); ?>
			<div class="form-group">
				<?= Html::activeTextInput($searchModel, 'keywords', ['class' => 'form-control', 'placeholder' => Yii::t('app/view', 'type_for_search')]); ?>
			</div>
			<div class="form-group">
				<?= Html::activeDropDownList($searchModel, 'role', ArrayHelper::map($optionsRole, 'id', 'name'), ['prompt' => Yii::t('app/view', 'all_roles'), 'class' => 'form-control']); ?>
			</div>
			<?= Html::submitButton('<i class="pli-magnifi-glass"></i> '.Yii::t('app/view', 'search'), ['class' => 'btn btn-default']); ?>
	    <?php ActiveForm::end(); ?>
    </div>
    <div class="col-sm-3 toolbar-right">
        <?= Html::a('<i class="pli-add""></i> '.Yii::t('app/view', 'add'), ['/admin/add'], ['class' => 'btn btn-purple']) ?>
    </div>
</div>

<div class="row">
	<?php foreach($dataProvider as $key => $model){ ?>
    <div class="col-sm-4 col-md-3">
        <div class="panel pos-rel">
        	<div class="widget-control text-right">
        		<label class="label label-<?= $model->status == Admin::STATUS_N ? 'success' : 'danger'; ?>"><?= Admin::getStatusArr($model->status); ?></label>
            </div>
            <div class="pad-all">
                <div class="media pad-ver">
                    <div class="media-left">
                        <img alt="Profile Picture" class="img-md img-circle" src="<?= empty($model->photo) ? Admin::getProfilePhotos(true) : $model->photo; ?>">
                    </div>
                    <div class="media-body pad-top">
                        <div class="box-inline">
                            <span class="text-lg text-semibold text-main"><?= isset($model->username) ? $model->username : '-'; ?></span>
                            <p class="text-sm"><?= isset($model->roles->name) ? Yii::t('app/menu', $model->roles->name) : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="pad-btm bord-bt text-sm">
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-id-card icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->name) ? Yii::t('app', 'not_set') : $model->admin->name; ?></div>
                	</div>
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-smartphone-3 icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->mobile) ? Yii::t('app', 'not_set') : $model->admin->mobile; ?></div>
                	</div>
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-email icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->email) ? Yii::t('app', 'not_set') : $model->admin->email; ?></div>
                	</div>
				</div>
                <div class="text-center pad-to">
                    <div class="btn-group">
                    	<?= Html::a('<i class="pli-pen"></i> '.Yii::t('app/view', 'edit'), ['/admin/edit', 'id' => $model->id], [
							"class" => "btn btn-sm btn-default"
						]); ?>
						<?= Html::a('<i class="pli-remove"></i> '.Yii::t('app/view', 'remove'), 'javascript:;', [
							'class' => 'btn btn-sm btn-default ajax-admin-delete',
						    'data-key' => $model->id,
						    'data-action' => Url::to(['/admin/delete'])
						]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php $this->beginBlock('admin');?>
$(function(){
	$('.ajax-admin-delete').click(function(){
		var that = $(this);
		var father = that.parent().parent().parent().parent().parent();
		showConfirm(_message.confirmDeleteTitle, _message.confirmDeleteTip, function (isConfirm) {
            if (isConfirm) {
            	showLoading(_message.loading);
            	$.ajax({
    				url: that.data('action'),
    				type: 'post',
    				data: {'id':that.data('key'), '_csrf':_csrf},
    				dataType: 'json',
    				success: function(result){
    					hideLoading();
    					if(result.result){
    						father.fadeOut('slow');
    					}else{
    						showMessage(result.message);
    					}  
    				},  
    				error: function(xhr) {
    					hideLoading();
    					if(xhr.status == '403'){
    						showMessage(_message.errorHttp403);
    					}else if(xhr.status == '404'){
    						showMessage(_message.errorHttp404);
    					}else{
    						showMessage(_message.errorHttp500);
    					}
    				}  
    			});
            } else {
                
            }
        }, {confirmButtonText: _message.confirmDelete, cancelButtonText: _message.confirmCancel, width: 400});
	});
})
<?php $this->endBlock();?>
<?php $this->registerJs($this->blocks['admin'], yii\web\View::POS_END);?>
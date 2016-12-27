<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//_print_r($_REQUEST)?>
<?//_print_r($arParams)?>
<?//_print_r($arResult)?>

<?//CMacro::Add('array');?> 

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/classes/ABaseClass.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/libs/underscore.js"></script>							
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/classes/list.model.js?<?=rand(0,999999)?>"></script>

<?
 // двигаем вниз
 if(isset($arResult['FIELDS']['DETAIL_TEXT'])){
	$addit = $arResult['FIELDS']['DETAIL_TEXT'];
	unset($arResult['FIELDS']['DETAIL_TEXT']);
	$arResult['FIELDS']['DETAIL_TEXT'] = $addit;
 }
 
 if(!empty($arResult['FIELDS']['PROPERTY_DEAL_TYPE'])){
	$res = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => __DEAL_TYPES_IBLOCK_ID, 'PROPERTY_WHERE_USED' => __DT_IN_ORDER), false, false, array('*', 'PROPERTY_LIST_NAME'));
	$arResult['FIELDS']['PROPERTY_DEAL_TYPE']['ITEMS'] = array();
	while($opt = $res->Fetch()){
		if(strlen($opt['PROPERTY_LIST_NAME_VALUE']))
			$opt['NAME'] = $opt['PROPERTY_LIST_NAME_VALUE'];
			
		$arResult['FIELDS']['PROPERTY_DEAL_TYPE']['ITEMS'][$opt['ID']] = $opt;
	}
 }
 
 //_print_r($arResult['FIELDS']['PROPERTY_TYPE']['ITEMS']);
 
 ?> 
	 
							<form class="orderForm" action="?" method="post">

								<?if(!empty($arResult['FIELDS']['PROPERTY_TYPE'])):?>
									<?
									 if(intval($arResult['FIELDS']['PROPERTY_TYPE']['VALUE'])){
										$selectedType = intval($arResult['FIELDS']['PROPERTY_TYPE']['VALUE']);
									 }else{
										$selectedType = intval($_REQUEST['type']);
										if(!$selectedType)
											// current() не работает
											foreach($arResult['FIELDS']['PROPERTY_TYPE']['ITEMS'] as $id=>$sect){
												$selectedType = $id; 
												break;
											}
									 }
									 if(!intval($arResult['FIELDS']['PROPERTY_DEAL_TYPE']['VALUE'])){
										$arResult['FIELDS']['PROPERTY_DEAL_TYPE']['VALUE'] = intval($_REQUEST['dtype']);
									 }
									 
									 //foreach($arResult['FIELDS']['PROPERTY_TYPE']['ITEMS'] as $id=>$val)
									 
									 ?>
									<div class="orderForm_Line">
										<div class="orderForm_selectTitle">Выберите тип недвижимости:</div>
										<div class="customSelectBlock" style="float: left;">
											<div class="customSelect"><span><?=$arResult['FIELDS']['PROPERTY_TYPE']['ITEMS'][$selectedType]['NAME']?></span><div class="customSelectBlock_Arrow"></div></div>
											<!--sublvl-->
											<div class="customSelectBlock_Sublvl">
												<?foreach($arResult['FIELDS']['PROPERTY_TYPE']['ITEMS'] as $id => $sect):?>
												<div class="cSB_sublvlLink<?=($id == $selectedType ? ' cSB_sublvlLink_Selected' : '')?>" value="<?=$id?>"><?=$sect['NAME']?></div>
												<?endforeach?>
											</div>
											<!--sublvl-->
										</div>
										<br class="clear_both">
									</div>
									<input type="hidden" id="field_type" name="<?=$arResult['FIELDS']['PROPERTY_TYPE']['INPUT_NAME']?>" value="<?=$selectedType?>" />
									<?
									 unset($arResult['FIELDS']['PROPERTY_TYPE']);
									 ?>
								<?endif?>
								
								<?if($arResult['RESULT']['FAIL']):?>
									<?if(!empty($arResult['ERROR_SUMMARY'])):?>
										<div class="error_message">Допущены ошибки при заполнении:</div>
										<ul class="error_list">
											<?foreach($arResult['ERROR_SUMMARY'] as $error):?>
												<li><?=$error?></li>
											<?endforeach?>
										</ul>
									<?endif?>
									<?if(!empty($arResult['DB_ERROR_SUMMARY'])):?>
										<div class="error_message">Сохранение данных не произошло:</div>
										<ul class="error_list">
											<?foreach($arResult['DB_ERROR_SUMMARY'] as $error):?>
												<li><?=$error?></li>
											<?endforeach?>
										</ul>
									<?endif?>
								<?elseif($arResult['RESULT']['SUCCESS']):?>
									<div class="success_message">Заявка успешно отправлена!</div>
								<?endif?>

								<div class="orderForm_lineDivider"></div>

								<?$i=0;?>
								<?foreach($arResult['FIELDS'] as $code => $prop):?>
								
									<?
									 if($code == 'PROPERTY_PHONE')
										$prop['NAME'] = 'Ваш телефон';
									 if($code == 'PROPERTY_EMAIL')
										$prop['NAME'] = 'Ваш Email';
									 ?>
								
									<?if($code == 'PROPERTY_PRICE') continue;?>
								
									<div class="orderForm_Line"<?=(!($i && $i%2) ? ' style="background: #eef5fb;"' : '')?>>
										<div class="orderForm_lineTitle"><?=$prop['NAME']?><?if($prop['REQUIRED']):?> <span class="formImportant">*</span><?endif?></div>
										
										<?if($code == 'PROPERTY_ROOMS'):?>
										
											<span<?=($prop['ERROR'] ? ' class="input_error"' : '')?>><input class="lineResult_Input only_numbers" style="margin: 0 0 0 10px; width: 20px;" type="text" value="<?=$prop['VALUE']?>" name="<?=$prop['INPUT_NAME']?>"></span>
											<div class="orderForm_lineResult<?=($arResult['FIELDS']['PROPERTY_PRICE']['ERROR'] ? ' input_error' : '')?>">
												<?=$arResult['FIELDS']['PROPERTY_PRICE']['NAME']?><?if($arResult['FIELDS']['PROPERTY_PRICE']['REQUIRED']):?> <span class="formImportant">*</span><?endif?><input class="lineResult_Input only_numbers" style="margin: 0 10px; width: 80px;" type="text" value="<?=$arResult['FIELDS']['PROPERTY_PRICE']['VALUE']?>" name="<?=$arResult['FIELDS']['PROPERTY_PRICE']['INPUT_NAME']?>">руб.
											</div>
										
										<?else:?>
										
										<div class="orderForm_lineResult<?=($prop['ERROR'] ? ' input_error' : '')?>">
												
												<?if($prop['ADVISED_TYPE'] == 'text'):?>
													<input class="lineResult_Input" type="text" value="<?=$prop['VALUE']?>" name="<?=$prop['INPUT_NAME']?>" />
												<?elseif($prop['ADVISED_TYPE'] == 'textarea'):?>
													<textarea class="lineResult_Textarea" name="<?=$prop['INPUT_NAME']?>"><?=$prop['VALUE']?></textarea>
												<?elseif($prop['ADVISED_TYPE'] == 'select'):?>
													<select class="customSelect01<?=($prop['ERROR'] ? ' input_error' : '')?>" name="<?=$prop['INPUT_NAME']?>">
														<?foreach($prop['ITEMS'] as $id => $item):?>
															<option value="<?=$id?>"<?=($id == $prop['VALUE'] ? ' selected' : '')?>><?=$item['NAME']?></option>
														<?endforeach?>
													</select>
													<div class="customSelect_Arrow"></div>
												<?endif?>
											
											<?/*<?endif?>*/?>
											
										</div>
										
										<?endif?>
										
										<br class="clear_both">
									</div>

									<div class="orderForm_lineDivider"></div>
									<?$i++;?>
								<?endforeach?>
								
								<?if($arParams['USE_CAPTCHA']):?>
									<div class="orderForm_Line"<?=(!($i && $i%2) ? ' style="background: #eef5fb;"' : '')?>>
										<?$APPLICATION->IncludeComponent("oneway:system.recaptcha", "order", Array(
											'WORD_INPUT_NAME' => $arResult['CAPTCHA']['WORD_INPUT_NAME'],
											'SID_INPUT_NAME' => $arResult['CAPTCHA']['SID_INPUT_NAME'],
											'FAILED' => $arResult['CAPTCHA']['ERROR']
										), false, array('HIDE_ICONS' => 'Y'));?>
									</div>
									<div class="orderForm_lineDivider"></div>
								<?endif?>
								
								<div class="orderForm_Line">
									<div class="orderForm_lineTitle">Поля отмеченные символом <span class="formImportant">*</span><br>обязательны для заполнения</div>
									<div class="orderForm_lineResult">
										<input class="formButton_Orange" style="margin: 6px 0 0 0; width: 100px;" type="submit" value="Отправить" name="<?=$arResult['FORM']['SUBMIT_INPUT_NAME']?>">
									</div>
									<br class="clear_both">
								</div>

						
								
							</form>
	<script type="text/javascript">
		(function(){
			CustomSelect($('.customSelect01'));
			
			var drop = new CSimpleDrop({
				block: '.customSelectBlock',
				handle: '.customSelect',
				drop: '.customSelectBlock_Sublvl',
				fadeSpeed: 100
			});
			$('.customSelectBlock_Sublvl div').click(function(){
				var $this = $(this);
				
				$('#field_type').val($this.attr('value'));
				$('.customSelect span').html($this.html());
				
				$('.cSB_sublvlLink').removeClass('cSB_sublvlLink_Selected');
				$this.addClass('cSB_sublvlLink_Selected');
				
				drop.close();
			});
			
		})();
	</script>

<?/*

<?if($arResult['RESULT']['SUCCESS']):?>
 <div class="request_form_success">Спасибо, ваша заявка принята!</div>
<?else:?>
 
<?if($arResult['RESULT']['FAIL']):?>
 <div class="request_form_fail">Были допущены ошибки при заполнении формы. Проверьте выделенные поля внизу.</div>
<?endif?>
 
<div class="generic_form_container">
 
 <form action="" method="POST" name="<?=$arParams['FORM_NAME']?>">

    <div class="generic_form_title">
     Регистрационные данные
    </div>

	<?foreach($arResult['FIELDS'] as $fid => $field):?>
	
	<?if($field['CODE'] == 'PROPERTY_PASS_SEREIS'):?>
	
    <div class="generic_form_title" style="margin-top: 15px">
     Паспортные данные
    </div>	
	
	<?endif?>
	
    <div class="reg_iform_label">
     <?=$field['NAME']?>: <?if($field['REQUIRED']):?><span class="generic_form_red_star">*</span><?endif?>
    </div>
    <div class="reg_iform_input">
	 <?=$field['ADVISED_HTML']?>
	</div>
	
	<?endforeach?>

   <?if($arParams["USE_CAPTCHA"] == "Y"):?>
    <div class="generic_form_title" style="margin-top: 15px">
     Защита от автоматической регистрации
    </div>
    <div class="generic_form_input">
	 <input type="hidden" name="<?=$arResult['CAPTCHA']['SID_INPUT_NAME']?>" value="<?=$arResult['CAPTCHA']['SID']?>">
     <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA']['SID']?>" width="180" height="40" alt="CAPTCHA">
	</div>
    <div class="generic_form_remark">
     Введите текст на картинке:
    </div>
	<div class="generic_form_input">
	 <input class="generic_form_text_captcha <?=($arResult['CAPTCHA']['ERROR'] ? $arParams['ERROR_CSS_CLASS'] : '')?>" type="text" name="<?=$arResult['CAPTCHA']['WORD_INPUT_NAME']?>" size="30" maxlength="50" value="">
	</div>
   <?endif?>
	
    <div class="generic_form_submit">
     <input type="submit" name="<?=$arResult['FORM']['SUBMIT_INPUT_NAME']?>" value="Отправить">
    </div>

   <div class="generic_form_label">   
    <span class="generic_form_red_star">*</span> &mdash; поля, обязательные для заполнения
   </div>
	
 </form>
 
</div>

<?endif?>

*/?>
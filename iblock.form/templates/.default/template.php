<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//_print_r($_REQUEST)?>
<?//_print_r($arResult['RESULT'])?>

<?//=ini_get('upload_max_filesize')?><br />
<?//=ini_get('post_max_size')?><br />

<?if($arResult['RESULT']['SUCCESS']):?>
 <div class="request_form_success">Спасибо, ваша заявка принята!</div>
<?else:?>
 
<?if($arResult['RESULT']['FAIL']):?>
 <div class="request_form_fail">Были допущены ошибки при заполнении формы. Проверьте выделенные поля внизу.</div>
<?endif?>
 
<div class="generic_form_container">
 
 <form <?=$arResult['FORM_ATTRIBUTES']?>>

	<?=$arResult['FORM_ADDITIONAL_FIELDS']?>
 
    <div class="generic_form_title">
     Регистрационные данные
    </div>

	<?foreach($arResult['FIELDS'] as $fid => $field):?>
	
		<?if($field['ADVISED_TYPE'] == 'hidden'):?>
			<?=$field['ADVISED_HTML']?>
			<?continue;?>
		<?endif?>
	
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
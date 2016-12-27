<?
// upd:
// class+component+lang/component

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

if(strlen($arParams["IBLOCK_SECTION_ID"])<=0)
	$arParams["IBLOCK_SECTION_ID"] = false;
else
	$arParams["IBLOCK_SECTION_ID"] = trim($arParams["IBLOCK_SECTION_ID"]);


if($arParams["ADD_ACTIVE"] == "N")
	$arParams["ACTIVE"] = "N";
else
	$arParams["ACTIVE"] = "Y";

########################################################################

CComponentTools::Escape($arParams['REQUEST_PREFIX']);
CComponentTools::Escape($arParams['ERROR_CSS_CLASS']);
CComponentTools::Escape($arParams['FORM_NAME']);

CComponentTools::Boolean($arParams["USE_CAPTCHA"]);
CComponentTools::Boolean($arParams["FETCH_OPTIONS"]);
CComponentTools::DropEmptyItems($arParams['FIELDS']);
CComponentTools::DropEmptyItems($arParams['PROPS']);

if(!strlen($arParams['FORM_NAME']))
	$arParams['FORM_NAME'] = 'form_iblock_'.$arParams["IBLOCK_ID"];

if(!strlen($arParams['REQUEST_PREFIX']))
	$arParams['REQUEST_PREFIX'] = $arParams['FORM_NAME'];

if($GLOBALS['USER']->IsAuthorized())
 $arParams["USE_CAPTCHA"] = false;

if(!strlen($arParams['ERROR_CSS_CLASS']))
 $arParams['ERROR_CSS_CLASS'] = 'request_form_error';
 
$ADMIN_PATH = 'http://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=#IBLOCK_ID#&type=#IBLOCK_TYPE#&ID=#MESSAGE_ID#';
 
########################################################################

	if(!CModule::IncludeModule("iblock"))
	{
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult = array();
	$iblock_f = CIBlock::GetFields($arParams['IBLOCK_ID']);
	
	foreach($arParams['FIELDS'] as $fld){
	
	 if(in_array($fld, array('PREVIEW_TEXT', 'DETAIL_TEXT')))
	  $type = 'textarea';
	 elseif(in_array($fld, array('PREVIEW_PICTURE', 'DETAIL_PICTURE')))
	  $type = 'file';
	 elseif($fld == 'IBLOCK_SECTION')
	  $type = 'select';
	 else
	  $type = 'text';
	
	 $arResult['FIELDS'][$fld] = array(
	  'CODE' => $fld,
	  'SYSTEM' => false,
	  'MULTIPLE' => false,
	  'REQUIRED' => $iblock_f[$fld]['IS_REQUIRED'] == 'Y',
	  'ADVISED_TYPE' => $type,
	  'NAME' => strlen($arParams[$fld.'_ALT']) ? $arParams[$fld.'_ALT'] : $iblock_f[$fld]['NAME'],
	  'INPUT_NAME' => $arParams['REQUEST_PREFIX'].'[FIELDS]['.$fld.']',
	  'VALUE' => htmlspecialchars($_REQUEST[$arParams['REQUEST_PREFIX']]['FIELDS'][$fld]), // это можно использовать для передачи в форму начальных значений
	  'IS_PROPERTY' => false
	 );
	}
	
	// тут надо узнать актуальный порядок свойств
	$selectedProps = array_flip($arParams['PROPS']);
	$usedProps = array();
	$res = CIBlockProperty::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams['IBLOCK_ID']));
	while($prop = $res->Fetch())
		if(isset($selectedProps[$prop['CODE']]))
			$usedProps[$prop['CODE']] = $prop;
			
	$propFiles = array();
	foreach($usedProps as $fld => $res){

	 $name = $res['NAME'];
	 unset($res['NAME']);
	 
     if(in_array($res['PROPERTY_TYPE'], array('L', 'E', 'G')))
	  $type = 'select';
	 elseif($res['PROPERTY_TYPE'] == 'F'){
	  $type = 'file';
	  $propFiles[] = 'PROPERTY_'.$fld;
	 }elseif($res['ROW_COUNT'] > 1)
	  $type = 'textarea';
	 else
	  $type = 'text';
	 
	 $arResult['FIELDS']['PROPERTY_'.$fld] = array(
	 
	  'CODE' => 'PROPERTY_'.$fld,
	  'SYSTEM' => $res,
	  'MULTIPLE' => $res['MULTIPLE'] == 'Y',
	  'VALUE' => '',
	  'REQUIRED' => $res['IS_REQUIRED'] == 'Y',
	  'NAME' => $name,
	  'ADVISED_TYPE' => $type,
	  'INPUT_NAME' => $arParams['REQUEST_PREFIX'].'[FIELDS][PROPERTY_'.$fld.']',
	  'VALUE' => htmlspecialchars($_REQUEST[$arParams['REQUEST_PREFIX']]['FIELDS']['PROPERTY_'.$fld]), // это можно использовать для передачи в форму начальных значений
	  'IS_PROPERTY' => true,
	  'TYPE' => $res['PROPERTY_TYPE']
	 
	 );
	 
	 if($res['PROPERTY_TYPE'] == 'E' && $arParams["FETCH_OPTIONS"]){
	  $list = array();
	  $elems = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $res['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'), false, false, array('ID', 'NAME'));
	  while($elem = $elems->Fetch())
	   $list[$elem['ID']] = $elem;
	  
	  $arResult['FIELDS']['PROPERTY_'.$fld]['ITEMS'] = $list;
	 }
	 if($res['PROPERTY_TYPE'] == 'L'){
	  $list = array();
	  $list_ = CIBlockPropertyEnum::GetList(array('sort' => 'asc'), array('CODE' => $fld, 'IBLOCK_ID' => $arParams['IBLOCK_ID']));
	  while($item = $list_->Fetch())
	   $list[$item['ID']] = array('NAME' => $item['VALUE']);
	   
	  $arResult['FIELDS']['PROPERTY_'.$fld]['ITEMS'] = $list;
	 }
	 if($res['PROPERTY_TYPE'] == 'G' && $arParams["FETCH_OPTIONS"]){
		$list = array();
		$elems = CIBlockSection::GetList(array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), array('IBLOCK_ID' => $res['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'));
		$elems->SetURLTemplates();
		while($elem = $elems->GetNext())
			$list[$elem['ID']] = $elem;
		  
		$arResult['FIELDS']['PROPERTY_'.$fld]['ITEMS'] = $list;	
	 }

	}
	
	if($arParams["USE_CAPTCHA"])
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php"); 
	
		$arResult["CAPTCHA"]["SID"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());
		$arResult["CAPTCHA"]["WORD_INPUT_NAME"] = $arParams['REQUEST_PREFIX'].'[CAPTCHA][WORD]';
		$arResult["CAPTCHA"]["SID_INPUT_NAME"] = $arParams['REQUEST_PREFIX'].'[CAPTCHA][SID]';
	} 
	
    $arResult['FORM']['SUBMIT_INPUT_NAME'] = $arParams['REQUEST_PREFIX'].'[SUBMIT]';
	
	// тут происходит суперсохранение суперданных суперформы
	if($_REQUEST[$arParams['REQUEST_PREFIX']]['SUBMIT']){
	
	 $were_errors = false;
	 $request = $_REQUEST[$arParams['REQUEST_PREFIX']];
	 $formFiles = $_FILES[$arParams['REQUEST_PREFIX']];
	 
	 foreach($arResult['FIELDS'] as $fid => $field){
	 
	  // проверим DETAIL_PICTURE и PREVIEW_PICTURE
	  if(!$field['IS_PROPERTY'] && ($field['CODE'] == 'PREVIEW_PICTURE' || $field['CODE'] == 'DETAIL_PICTURE')){
		if(($feCode = _CIBlockFormTools::BadFile($field['CODE'], $formFiles, $arResult))){
			$arResult['ERROR_SUMMARY'][] = str_replace(array('#PROP_NAME#', '#FAIL_REASON#'), array($field['NAME'], GetMessage(_CIBlockFormTools::ExplainFileError($feCode))), GetMessage('AW_IBFORM_FILE_FAIL'));
			$were_errors = true;
		}
	  }
	 
	  $arResult['FIELDS'][$fid]['VALUE'] = htmlspecialchars($request['FIELDS'][$field['CODE']]);
	  if(!strlen($arResult['FIELDS'][$fid]['VALUE']) && $field['REQUIRED']){
	   $arResult['FIELDS'][$fid]['ERROR'] = true;
	   $arResult['ERROR_SUMMARY'][] = str_replace('#PROP_NAME#', $field['NAME'], GetMessage('AW_IBFORM_NAME'));
	   $were_errors = true;
	  }
	  if(
		$fid == 'PROPERTY_EMAIL'
		&& 
		strlen($arResult['FIELDS'][$fid]['VALUE'])
		&& 
		!preg_match('#^[^\s]+@[^\s]+\.[^\s]+$#', $arResult['FIELDS'][$fid]['VALUE'])){ // cool non-documented feature :)
			$arResult['ERROR_SUMMARY'][] = GetMessage('AW_IBFORM_WRONG_EMAIL');
			$arResult['FIELDS'][$fid]['ERROR'] = true;
			$were_errors = true;
	  }
	  // свойства-файлы (тут надо ещё поддержку множественного свойства типа "файл")
	  if($field['TYPE'] == 'F' && ($feCode = _CIBlockFormTools::BadFile($field['CODE'], $formFiles, $arResult))){
		
		$arResult['ERROR_SUMMARY'][] = str_replace(array('#PROP_NAME#', '#FAIL_REASON#'), array($field['NAME'], GetMessage(_CIBlockFormTools::ExplainFileError($feCode))), GetMessage('AW_IBFORM_FILE_FAIL'));
		
		$were_errors = true;
	  }
	 }
	 
	 // проверяем капчу
	 if(
	    $arParams["USE_CAPTCHA"]
		&&
		!$APPLICATION->CaptchaCheckCode($request["CAPTCHA"]["WORD"], $request['CAPTCHA']['SID'])
	 ){
	  $were_errors = true;
	  $arResult['CAPTCHA']['ERROR'] = true;
	  $arResult['ERROR_SUMMARY'][] = GetMessage('AW_IBFORM_WRONG_CAPTCHA');
	 }

	 // теперь можем отправлять сообщение и добавлять в инфоблок, если не было ошибок
	 if(!$were_errors){
	 
	  $files = array();
	  $fields = array();
	  $props = array();
	  $mixed = array();
	  foreach($arResult['FIELDS'] as $fid => $field){
	  
		if(empty($field['CODE']))
			continue;
	  
	   // обрабатываем файлы...
	   if(($field['TYPE'] == 'F' && !empty($formFiles['name']['FIELDS'][$field['CODE']]))
		||
		(!$field['IS_PROPERTY'] && ($field['CODE'] == 'PREVIEW_PICTURE' || $field['CODE'] == 'DETAIL_PICTURE'))){
			$field['VALUE'] = CFile::MakeFileArray($formFiles['tmp_name']['FIELDS'][$field['CODE']]);
			/*, $formFiles['type']['FIELDS'][$field['CODE']]*/
			$field['VALUE']['name'] = $formFiles['name']['FIELDS'][$field['CODE']];
	   }
	  
	   // остальные значения
	   if($field['IS_PROPERTY'])
	    $props[$field['SYSTEM']['CODE']] = $field['VALUE'];
	   else
	    $fields[$field['CODE']] = $field['VALUE'];
		
	   $val = $field['VALUE'];
	   // надо сформировать корректные строковые значения для письма
	   if(in_array($field['TYPE'], array('L','G','E'))){
		if($arParams["FETCH_OPTIONS"])
			$val = $field['ITEMS'][$val]['NAME'].' ['.$val.']';
		else{
		
			if($field['TYPE'] == 'E'){ // element
				$res = CIBlockElement::GetByID($val);
				$res = $res->Fetch();
				$val = $res['NAME'].' ['.$res['ID'].']';
			}
		}
	   }
		
	   $mixed[$field['CODE']] = $val;
	  }
	  
	  // сохранение в инфоблок	  
	  $element = array(
		"MODIFIED_BY" => $USER->GetID(),
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"IBLOCK_SECTION_ID" => $arParams['IBLOCK_SECTION_ID'],
        "ACTIVE"  => $arParams["ACTIVE"],
		"ACTIVE_FROM" => ConvertTimeStamp(time(), 'FULL'),
		"CODE" => crc32($fields["NAME"].rand(10000,99999))
	  );
	  $element = array_merge($element, $fields);
	  $element['PROPERTY_VALUES'] = $props;
	  
	  $elem = new CIBlockElement();
	  $result = $elem->Add($element);
	  
		if(!$result){
			$arResult['DB_ERROR_SUMMARY'][] = strip_tags($elem->LAST_ERROR);
			$arResult['RESULT']['FAIL'] = true;
		}else{
	  
			$mixed['MESSAGE_ID'] = $result;
			$mixed['MESSAGE_NAME'] = $mixed['NAME'];
			$mixed['LINK'] = str_replace(array(
											'#IBLOCK_ID#',
											'#IBLOCK_TYPE#',
											'#MESSAGE_ID#'
											), array(
											 $arParams['IBLOCK_ID'],
											 $arParams['IBLOCK_TYPE'],
											 $result
											), $ADMIN_PATH);
		
			// надо ещё файлы достать... это +2 запроса
			$res = CIBlockElement::GetList(false, array('ID' => $result), false, false, array_merge(array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'), $propFiles));
			$files = array();
			$filesByProps = array_flip($propFiles);
			// сразу сделаем с запасом на множественность свойств
			while($item = $res->Fetch()){
				if(intval($item['PREVIEW_PICTURE'])){
					$files[] = $item['PREVIEW_PICTURE'];
					$filesByProps['PREVIEW_PICTURE'] = array();
					$filesByProps['PREVIEW_PICTURE'][] = $item['PREVIEW_PICTURE'];
				}
				if(intval($item['DETAIL_PICTURE'])){
					$files[] = $item['DETAIL_PICTURE'];
					$filesByProps['DETAIL_PICTURE'] = array();
					$filesByProps['DETAIL_PICTURE'][] = $item['DETAIL_PICTURE'];
				}
				foreach($propFiles as $pf)
					if(!empty($item[$pf.'_VALUE'])){
						$filesByProps[$pf] = array();
						if(is_array($item[$pf.'_VALUE'])){
							$files = array_merge($files, $item[$pf.'_VALUE']);
							$filesByProps = array_merge($item[$pf.'_VALUE'], $filesByProps[$pf]);
						}else{
							$files[] = $item[$pf.'_VALUE'];
							$filesByProps[$pf][] = $item[$pf.'_VALUE'];
						}
					}
			}
			
			$files = array_unique($files);
			$res = CFile::GetList(false, array('@ID' => implode(',', $files)));
			$files = array();
			while($file = $res->Fetch())
				$files[$file['ID']] = $file;
						
			$propFiles[] = 'PREVIEW_PICTURE';
			$propFiles[] = 'DETAIL_PICTURE';
			$siteRoot = 'http://'.$_SERVER['SERVER_NAME'].'/';
			foreach($propFiles as $pf){
				$files_ = array();
				foreach($filesByProps[$pf] as $fId){
					$files_[] = $files[$fId]['FILE_NAME'].' [<a href="'.$siteRoot.'upload/'.$files[$fId]['SUBDIR'].'/'.$files[$fId]['FILE_NAME'].'">'.GetMessage('AW_DOWNLOAD').'</a>]';
				}
				$mixed[$pf] = implode(', ', $files_);
			}
		
			if(strlen($arParams['EVENT_TYPE'])){
				$event = new CEvent;
				$eid = $event->Send($arParams['EVENT_TYPE'], SITE_ID, $mixed);
			}
		  		  
		  
		  /*if(strlen($arParams['SUCCESS_URL'])){
		   LocalRedirect($arParams['SUCCESS_URL']);
		   exit();
		  }*/
		  
		  $arResult['RESULT']['ID'] = $result;
		  $arResult['RESULT']['SUCCESS'] = true;
	  }
	 }
	 else
	  $arResult['RESULT']['FAIL'] = true;
	}

	// сгенерируем-ка HTML-код
	$wereFiles = false;
	foreach($arResult['FIELDS'] as $fid => $o)
	{
	 $err_class = '';//'class="#CLASS_HERE#'.($o['ERROR'] ? ' '.$arParams['ERROR_CSS_CLASS'] : '').'"';
	
	 if($o['ADVISED_TYPE'] == 'select')
	 {
	  $html = '<select name="'.$o['INPUT_NAME'].'"'.$err_class.'>';
	  foreach($o['ITEMS'] as $id=>$val)
	   $html .= '<option value="'.$id.'"'.($val == $o['VALUE'] ? ' selected' : '').'>'.$val.'</option>';
	  $html .= '</select>';
	 }
	 elseif($o['ADVISED_TYPE'] == 'file'){
	  $html = '<input type="file" name="'.$o['INPUT_NAME'].'" value=""'.$err_class.' />';
	  $wereFiles = true;
	 }elseif($o['ADVISED_TYPE'] == 'textarea')
	  $html = '<textarea name="'.$o['INPUT_NAME'].'"'.$err_class.'>'.$o['VALUE'].'</textarea>';
	 else
	 {
	  $html = '<input type="text" name="'.$o['INPUT_NAME'].'" value="'.$o['VALUE'].'"'.$err_class.' ';
	  
	  if($o['SYSTEM']['USER_TYPE'] == 'DateTime')
	  {
	   ob_start();
	   $APPLICATION->IncludeComponent(
										'bitrix:main.calendar',
										$arParams['CALENDAR_TEMPLATE'],
										array(
											'FORM_NAME' => $arParams['FORM_NAME'],
											'INPUT_NAME' => $o['INPUT_NAME'],
											'INPUT_VALUE' => '',
										),
										null,
										array('HIDE_ICONS' => 'Y')
									);
		$html .= ' appearance="datetime" />'.ob_get_contents();
		ob_end_clean();
									
	  }
	  else
	   $html .= ' />';
	 }
	 
     $arResult['FIELDS'][$fid]['ADVISED_HTML'] = $html;
	}
	
	$arResult['FORM_ATTRIBUTES'] = 'action="" method="POST" name="'.$arParams['FORM_NAME'].'"'.($wereFiles ? '  enctype="multipart/form-data"' : '');
	$arResult['MAX_FILE_SIZE'] = 30000000; // потом брать из ini_get('upload_max_filesize');
	if($wereFiles)
		$arResult['FORM_ADDITIONAL_FIELDS'] = '<input type="hidden" name="'.$arResult['MAX_FILE_SIZE'].'" value="'.$maxFile.'" />';
		
	$this->IncludeComponentTemplate();

?>
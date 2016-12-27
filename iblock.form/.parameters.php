<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

#######################################################	
	
$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

#######################################################	
	
$arProps = array();
$rsProp = CIBlockProperty::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));

$allowed_types = array('S', 'F', 'N', 'L', 'G', 'E');

while ($arr=$rsProp->Fetch()){

	if(in_array($arr["PROPERTY_TYPE"], $allowed_types))
		$arProps[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

#######################################################

$arFields = array(
	"NAME" => GetMessage("AW_IBFORM_ADD_NAME"),
	"TAGS" => GetMessage("AW_IBFORM_ADD_TAGS"),
	"ACTIVE_FROM" => GetMessage("AW_IBFORM_ADD_ACTIVE_FROM"),
	"ACTIVE_TO" => GetMessage("AW_IBFORM_ADD_ACTIVE_TO"),
	"IBLOCK_SECTION" => GetMessage("AW_IBFORM_ADD_IBLOCK_SECTION"),
	"PREVIEW_TEXT" => GetMessage("AW_IBFORM_ADD_PREVIEW_TEXT"),
	"PREVIEW_PICTURE" => GetMessage("AW_IBFORM_ADD_PREVIEW_PICTURE"),
	"DETAIL_TEXT" => GetMessage("AW_IBFORM_ADD_DETAIL_TEXT"),
	"DETAIL_PICTURE" => GetMessage("AW_IBFORM_ADD_DETAIL_PICTURE"),
	"SORT" => GetMessage("AW_IBFORM_ADD_SORT"),
	"XML_ID" => GetMessage("AW_IBFORM_ADD_XML_ID"),
	"CODE" => GetMessage("AW_IBFORM_ADD_CODE")
);
	
#######################################################	
	
$ets = array();
$res = CEventType::GetList(array('LID' => LANGUAGE_ID));
while($et = $res->Fetch())
 $ets[$et['EVENT_NAME']] = '['.$et['EVENT_NAME'].'] '.$et['NAME'];

#######################################################
 
$arComponentParameters = array(
	"GROUPS" => array(
		"ALT_NAMES" => array(
			"NAME" => GetMessage("AW_IBFORM_ALT_NAMES"),
			"SORT" => "500"
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AW_IBFORM_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AW_IBFORM_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"FIELDS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AW_IBFORM_IBLOCK_FIELD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"PROPS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AW_IBFORM_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProps,
			"ADDITIONAL_VALUES" => "Y",
		),
		"FETCH_OPTIONS" => Array(
			"NAME" => GetMessage("AW_FETCH_OPTIONS"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "ADDITIONAL",
		),
		"EVENT_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AW_IBFORM_EVENT_TYPES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $ets,
			"ADDITIONAL_VALUES" => "N",
		),
		"REQUEST_PREFIX" => array(
			"PARENT" => "ADDITIONAL",
			"NAME" => GetMessage("AW_IBFORM_REQUEST_PREFIX"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("AW_IBFORM_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"FORM_NAME" => array(
			"PARENT" => "ADDITIONAL",
			"NAME" => GetMessage("AW_IBFORM_FORM_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CALENDAR_TEMPLATE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("AW_IBFORM_CALENDAR_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SUCCESS_URL" => array(
			"PARENT" => "ADDITIONAL",
			"NAME" => GetMessage("AW_IBFORM_SUCCESS_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ERROR_CSS_CLASS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("AW_IBFORM_ERROR_CSS_CLASS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		/*
		"INPUT_TEXT_CSS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("AW_IBFORM_IT_CSS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SELECT_CSS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("AW_IBFORM_SEL_CSS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"FILE_CSS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("AW_IBFORM_FILE_CSS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		*/
	),
);

foreach($arFields as $k=>$v)
{
 $arComponentParameters['PARAMETERS'][$k.'_ALT'] = array(
			"PARENT" => "ALT_NAMES",
			"NAME" => $v,
			"TYPE" => "STRING",
			"DEFAULT" => "",
		);
}
?>

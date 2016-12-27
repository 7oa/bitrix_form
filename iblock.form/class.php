<?
 if(!class_exists('CComponentTools')){

	 class CComponentTools {
		public static function Boolean(&$fld){
			$fld = $fld == 'Y';
			return $fld;
		}
		
		public static function Int(&$fld, $default){
			$fld = intval($fld);
			if(!$fld && isset($default))
				$fld = $default;
				
			return $fld;
		}
		
		public static function DropEmptyItems(&$arr){
			if(!is_array($arr)) return false;
			foreach($arr as $k=>$v)
				if(!strlen($v))
					unset($arr[$k]);		
					
			return $arr;
		}
		
		public static function Escape($fld){
			return htmlspecialchars($fld);
		}
		
		public static function DropEmpty(&$arr){
			foreach($arr as $k=>$v)
				if(!strlen($v))
					unset($arr[$k]);  
		}
	 }
 }
 if(!class_exists('_CIBlockFormTools')){

	class _CIBlockFormTools {
		public static function FormatEventDescription(&$arResult){
			$out = '';
			foreach($arResult['FIELDS'] as $code => $val)
				$out .= '#'.$code.'# - '.$val['NAME']."\r\n";
				
			$out .= "#MESSAGE_ID# - ".GetMessage('AW_MESSAGE_ID')."\r\n";
			$out .= "#MESSAGE_NAME# - ".GetMessage('AW_MESSAGE_NAME')."\r\n";
			$out .= "#LINK# - ".GetMessage('AW_LINK')."\r\n";
				
			return $out;
		}
		public static function FormatMailTemplate(&$arResult){
			$out = '';
			foreach($arResult['FIELDS'] as $code => $val)
				$out .= $val['NAME'].":\r\n<br />#".$code."#<br />\r\n<br />\r\n";
				
			$out .= GetMessage('AW_MESSAGE_ID').":\r\n<br />#MESSAGE_ID#<br />\r\n<br />\r\n";	
			$out .= GetMessage('AW_MESSAGE_NAME').":\r\n<br />#MESSAGE_NAME#<br />\r\n<br />\r\n";
			$out .= GetMessage('AW_LINK').":\r\n<br />#LINK#<br />\r\n<br />\r\n";
				
			return $out;
		}
		public static function BadFile($code, &$formFiles, &$result){
			$failCode = $formFiles['error']['FIELDS'][$code];
			if(!empty($formFiles['name']['FIELDS'][$code]) && $failCode != 0){
				$result['FIELDS'][$code]['ERROR'] = true;
				$result['FIELDS'][$code]['FILE_ERROR'] = $failCode;
				return $failCode;
			}
			return false;
		}
		public function ExplainFileError($code){
			if(in_array($code, array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE)))
				return 'AW_FE_REASON_SIZE';
			else
				return 'AW_FE_REASON_INTERNAL'; 
		} 		
	}
 }	 
 ?>
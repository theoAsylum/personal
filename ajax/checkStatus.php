<?
	set_time_limit(0);
	ignore_user_abort(true);
	#-------------------------------------------------------------------------------------
	define('NO_KEEP_STATISTIC', true);
	define('NOT_CHECK_PERMISSIONS',true);
	define('BX_CRONTAB', true);
	define('BX_NO_ACCELERATOR_RESET', true);
	define("CACHED_b_iblock_bucket_size", 3600);
	define('SITE_ID', 's1');
	#-------------------------------------------------------------------------------------
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	#-------------------------------------------------------------------------------------
	use Bitrix\Main\Mail\Event;
	use Bitrix\Main\Application;
	use Bitrix\Main\Loader;
	#-------------------------------------------------------------------------------------
	$request = Application::getInstance()->getContext()->getRequest();
	Loader::includeModule('iblock');
	#-------------------------------------------------------------------------------------
	$arFields = ["name","surname","name","second_name","date","inn","email","phone","LEAD"];
	$arPost = [];
	foreach($arFields as $field){
		$val = htmlspecialcharsbx($request->getPost($field));
		if($val) $arPost[$field] = $val;
	}
	if($arFields['phone']){
        $arFields['phone'] = str_replace([' ', '(', ')', '-', '_', '+'], '', $arFields['phone']);
        $arFields['phone'] = substr($arFields['phone'], 1);
    }
    $arCookies = apiAuthorize();
	if($arCookies){
	    if($arPost['LEAD']){
            $arResponse=getPredscoringStatus($arCookies,$arPost['LEAD']);
        }else{
            $arResponse=sendApiPredscoring($arCookies,$arPost);
        }
		if($arResponse){
		    if($arResponse['status'] != 'waiting' && $arResponse['lead']){
                if($arPost['inn']){
                    $companyId = getCompanyByInn($arPost['inn']);
                    CIBlockElement::SetPropertyValuesEx($companyId, getIblockIdByCode('company'), array('LEAD'=>$arResponse['lead'],'STATUS'=>$arResponse['status']));
                }
            }
			echo json_encode(array('success'=>true,'status'=>$arResponse['status']),JSON_UNESCAPED_UNICODE);
			exit();
		}else{
			echo json_encode(array('success'=>false,'title'=>'Ошибка ответа'),JSON_UNESCAPED_UNICODE);
			exit();
		}
	}else{
		echo json_encode(array('success' => false, 'title' => 'Ошибка соединения'), JSON_UNESCAPED_UNICODE);
		exit();
	}
?>
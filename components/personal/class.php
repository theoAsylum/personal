<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Page\Asset;

class PersonalFourPx extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams){

        if (!$arParams['PERCENT_FOR_INN']) {
            $arParams['PERCENT_FOR_INN'] = 53;
        }
        if (!$arParams['PERCENT_FOR_CAR']) {
            $arParams['PERCENT_FOR_CAR'] = 27;
        }
        if (!$arParams['PERCENT_FOR_FILES']) {
            $arParams['PERCENT_FOR_FILES'] = 20;
        }
        if (!$arParams['MAX_COUNT_AUTO']) {
            $arParams['MAX_COUNT_AUTO'] = 5;
        }
        if (!$arParams['MAX_COUNT_FILES']) {
            $arParams['MAX_COUNT_FILES'] = 5;
        }
        if (!$arParams['MAX_COUNT_COMPANIES']) {
            $arParams['MAX_COUNT_COMPANIES'] = 5;
        }
        if (!$arParams['MAX_SIZE_FILES']) {
            $arParams['MAX_SIZE_FILES'] = 5000;
        }

        return $arParams;
    }

    public $arFieldsName = [
        'phone' => 'Телефон', 'email' => 'Email', 'name' => 'Имя', 'surname' => 'Фамилия', 'second_name' => 'Отчество', 'inn' => 'ИНН организации', 'org_name' => 'Название организации', 'date' => 'Дата рождения', 'policy' => 'Политика конфиденциальности', 'BRAND' => 'Марка автомобиля', 'MODEL' => 'Модель автомобиля', 'TIME' => 'Желаемый срок лизинга', 'FIRST_PAYMENT' => 'Размер первого платежа'
    ];

    public function checkPhone($phone){
        $phone = str_replace([' ', '(', ')', '-', '_', '+'], '', $phone);
        $first = $phone[0];
        if($first == '8'){
            $phone = '7'.substr($phone, 1);
        }
        if(strlen($phone) == 11 && $phone[0] == '7'){
            return $phone;
        }else{
            return false;
        }
    }

    public function checkGoogleRecaptcha($response){
        $httpClient = new \Bitrix\Main\Web\HttpClient;
        $result = $httpClient->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => GOOGLE_RECAPTCHA_SECRET_KEY,
                'response' => $response,
                'remoteip' => $_SERVER['HTTP_X_REAL_IP']
            ]
        );
        $result = json_decode($result, true);

        if ($result['success'] !== true) {
            return false;
        }else{
            return true;
        }
    }

    public function getUserByPhone($phone){
        $resUser = CUser::GetList($by = "SORT", $order = "ASC", array("PERSONAL_MOBILE" => $phone, "ACTIVE" => "Y"), ['SELECT' => ['ID','UF_SHORT_PASS','UF_SHORT_PASS_TIME']]);
        if($arUser = $resUser->Fetch()){
            $arResult['UF_SHORT_PASS_TIME'] = (int)$arUser['UF_SHORT_PASS_TIME'];
            $arResult['UF_SHORT_PASS'] = $arUser['UF_SHORT_PASS'];
            $arResult['ID'] = $arUser['ID'];
            return $arResult;
        }else{
            return false;
        }
    }

    public function getUserByEmail($email){
        $email = trim(filter_var($email,FILTER_SANITIZE_STRING));
        $resUser = CUser::GetList($by = "SORT", $order = "ASC", array("LOGIN" => $email, "ACTIVE" => "Y"), ['SELECT' => ['ID','UF_SHORT_PASS','UF_SHORT_PASS_TIME']]);
        if($arUser = $resUser->Fetch()){
            $arResult['UF_SHORT_PASS_TIME'] = (int)$arUser['UF_SHORT_PASS_TIME'];
            $arResult['UF_SHORT_PASS'] = $arUser['UF_SHORT_PASS'];
            $arResult['ID'] = $arUser['ID'];
            return $arResult;
        }else{
            return false;
        }
    }

    public function getUserArById($userId){
        $resUser = CUser::GetList($by = "SORT", $order = "ASC", array("ID" => $userId, "ACTIVE" => "Y"), ['FIELDS' => ['ID','PERSONAL_MOBILE','EMAIL','LOGIN','NAME','LAST_NAME','SECOND_NAME','PERSONAL_BIRTHDAY']]);
        if($arUser = $resUser->Fetch()){
            return $arUser;
        }else{
            return false;
        }
    }

    public function clearRequestFields($arFields, $arReqFields){
        if($arReqFields && $arFields){
            $arResult = [];
            foreach($arReqFields as $fieldCode){
                $val = trim(filter_var($arFields[$fieldCode],FILTER_SANITIZE_STRING));
                $arResult[$fieldCode] = $val;
            }
            return $arResult;
        }
    }

    public function checkRequestFields($arFields){
        if($arFields){
            $arError = [];
            $arFieldsError = [];
            foreach($arFields as $fieldCode => $val){
                if($val == ''){
                    $arFieldsError[] = $this->arFieldsName[$fieldCode];
                }elseif($fieldCode == 'phone'){
                    if(!self::checkPhone($val)){
                        $arError[] = 'Неверный формат номера телефона.';
                    }
                }elseif($fieldCode == 'date'){
                    $val = preg_replace('/[^0-9\.]/u', '', trim($val));
                    $val_ar = explode('.', $val);
                    if(!@checkdate($val_ar[1], $val_ar[0], $val_ar[2])){
                        $arError[] = 'Неверный формат даты рождения.';
                    }
                }elseif($fieldCode == 'inn'){
                    if(strlen($val) != 10 && strlen($val) != 12){
                        $arError[] = 'Неверный формат ИНН.';
                    }
                }elseif($fieldCode == 'email'){
                    if(!check_email($val)){
                        $arError[] = 'Неверный формат email.';
                    }
                }
            }
            if($arFieldsError){
                $arError[] = 'Вы должны заполнить поля: '.implode(', ', $arFieldsError).'.';
            }
            if($arError){
                return $arError;
            }else{
                return true;
            }
        }
        return false;
    }

    public function checkApiResponse($status){
        $exist = false;
        switch($status){
            case 'positive':
                $template = 'success';
                break;
            case 'current';
                $template = 'success';
                $exist = true;
                break;
            case 'waiting';
                $template = 'waiting';
                break;
            case 'negative';
                $template = 'fail';
                break;
            default:
                $template = 'fail';
                break;
        }
        return ['TEMPLATE'=>$template,'USER_EXIST'=>$exist];
    }

    public function registrationUser($arFields){
        $pass = randString(7);
        $user = new CUser;
        $id = $user->Add([
            'LOGIN' => $arFields['email'],
            'NAME' => $arFields['name'],
            'LAST_NAME' => $arFields['surname'],
            'SECOND_NAME' => $arFields['second_name'],
            'EMAIL' => $arFields['email'],
            'PASSWORD' => $pass,
            'CONFIRM_PASSWORD' => $pass,
            'PERSONAL_MOBILE' => $arFields['phone'],
            'PERSONAL_BIRTHDAY' => ConvertTimeStamp(strtotime($arFields['date']), "FULL", 's1'),
        ]);
        return $id;
    }

    public function changeUser($userId, $arForm){

        $user = new CUser;
        $res = $user->Update($userId, array('NAME' => $arForm['name'], 'LAST_NAME' => $arForm['surname'], 'SECOND_NAME'=> $arForm['second_name'], 'PERSONAL_BIRTHDAY' => ConvertTimeStamp(strtotime($arForm['date']), "FULL", 's1')));

        if($res){
            return true;
        }else{
            return false;
        }

    }

    public function getUserCompaniesCount($userId,$iblockId){

        $arFilter = [
            'IBLOCK_ID'=>$iblockId,
            'ACTIVE'=>'Y',
            '=PROPERTY_USER'=> $userId,
        ];
        $res = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, ['ID', 'NAME']);
        return $res->SelectedRowsCount();
    }

    public function addCompany($arFields, $iblockId, $idUser, $arStatus = []){
        $el = new CIBlockElement;
        return $el->Add([
            "MODIFIED_BY"       => 1,
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID"      => $iblockId,
            "PROPERTY_VALUES"=> [
                'USER' => $idUser,
                'INN'  => $arFields['inn'],
                'STATUS'  => $arStatus['status']?$arStatus['status']:'',
                'LEAD'  => $arStatus['lead']?$arStatus['lead']:'',
            ],
            "NAME"              => $arFields['org_name'],
            "ACTIVE"            => "Y",
        ]);
    }

    public function checkCompanyId($companyId, $iblockId, $userId){

        $arFilter = [
            'IBLOCK_ID'=>$iblockId,
            'ACTIVE'=>'Y',
            '=PROPERTY_USER'=> $userId,
            '=ID'=> $companyId,
        ];
        $res = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, ['ID', 'NAME', 'IBLOCK_ID']);
        if($resCompany = $res->GetNextElement(true, false)){
            $arCompany = $resCompany->GetFields();
            $arCompany['NAME'] = htmlspecialchars_decode($arCompany['NAME']);
            $arCompany['PROPERTIES'] = $resCompany->GetProperties();
            return $arCompany;
        }else{
            return false;
        }

    }

    public function getCompanyId($userId, $iblockId){

        $arFilter = [
            'IBLOCK_ID'=>$iblockId,
            'ACTIVE'=>'Y',
            '=PROPERTY_USER'=> $userId,
        ];
        $res = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, ['nTopCount' => 1], ['ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_AUTO', 'PROPERTY_FILES', 'PROPERTY_FILES_1']);
        if($resCompany = $res->GetNextElement(true, false)){
            $arCompany = $resCompany->GetFields();
            $arCompany['NAME'] = htmlspecialchars_decode($arCompany['NAME']);
            $arCompany['PROPERTIES'] = $resCompany->GetProperties();
            return $arCompany;
        }else{
            return false;
        }

    }

    public function getPercent($auto, $files, $files_1){
        $res = $this->arParams['PERCENT_FOR_INN'];
        if($auto) $res += $this->arParams['PERCENT_FOR_CAR'];
        if($files || $files_1) $res += $this->arParams['PERCENT_FOR_FILES'];

        return $res;
    }

    public function setCompanyArray($arCompany){
        $percent = self::getPercent(!empty($arCompany['PROPERTIES']['AUTO']['VALUE']), !empty($arCompany['PROPERTIES']['FILES']['VALUE']), !empty($arCompany['PROPERTIES']['FILES_1']['VALUE']));

        return [
            'ID' => $arCompany['ID'],
            'NAME' => htmlspecialchars_decode($arCompany['NAME']),
            'AUTO' => $arCompany['PROPERTIES']['AUTO']['VALUE'],
            'FILES' => ['VALUE' => $arCompany['PROPERTIES']['FILES']['VALUE'],'NAME'=> $arCompany['PROPERTIES']['FILES']['NAME'], 'VALUE_FOR_DEL' => $arCompany['PROPERTIES']['FILES']['PROPERTY_VALUE_ID']],
            'FILES_1' => ['VALUE' => $arCompany['PROPERTIES']['FILES_1']['VALUE'],'NAME'=> $arCompany['PROPERTIES']['FILES_1']['NAME'], 'VALUE_FOR_DEL' => $arCompany['PROPERTIES']['FILES_1']['PROPERTY_VALUE_ID']],
            'PERCENT' => $percent,
            'INN' => $arCompany['PROPERTIES']['INN']['VALUE'],
            'LEAD' => $arCompany['PROPERTIES']['LEAD']['VALUE']
        ];
    }

    public function getFormOptions($iblockId){

        $obCache = new CPHPCache();
        $cacheID = 'formOpt'.$iblockId;
        $cacheLifetime = 86400;
        $cachePath = '/anketa/formOpt';
        if ( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) ) {
            $arProps = $obCache->GetVars();
        }else{
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cachePath);
            $CACHE_MANAGER->RegisterTag('iblock_id_'.$iblockId);
            $CACHE_MANAGER->RegisterTag('iblock_id_'.getIblockIdByCode('brand'));
            $CACHE_MANAGER->RegisterTag('iblock_id_'.getIblockIdByCode('model'));

            $arProps = [];
            $rsProp = CIBlockProperty::GetList([], ["ACTIVE"=>"Y", "IBLOCK_ID"=>getIblockIdByCode('auto')]);
            while ($arProp = $rsProp->Fetch()){
                $arTemp = array();
                if ($arProp["PROPERTY_TYPE"]=="L"){
                    $rsEnum = CIBlockProperty::GetPropertyEnum($arProp["ID"]);
                    while ($arEnum = $rsEnum->Fetch()) $arTemp[$arEnum["ID"]] = $arEnum["VALUE"];
                }
                elseif ($arProp["PROPERTY_TYPE"]=="E" && $arProp['CODE'] != 'COMPANY'){
                    $rsEls = CIBlockElement::GetList(['SORT'=>'ASC','NAME'=>'ASC'], array("IBLOCK_ID"=>$arProp["LINK_IBLOCK_ID"], "ACTIVE"=>"Y"), false, false, array("ID","NAME","IBLOCK_ID"));
                    while ($arEl = $rsEls->GetNextElement()){
                        $arElement = $arEl->GetFields();
                        $arElement['PROPERTIES'] = $arEl->GetProperties();
                        $arTemp[$arElement["ID"]] = $arElement;
                    }
                }
                if($arTemp) $arProps[$arProp["CODE"]] = $arTemp;
                unset($arTemp);
            }
            if($arProps['BRAND']){
                foreach($arProps['BRAND'] as $id=>$arBrand){
                    $arModels = [];
                    foreach($arBrand['PROPERTIES']['MODEL']['VALUE'] as $modelId){
                        $arModels[] = $modelId;
                    };
                    $arBrands[$id] = ['NAME' => $arBrand['NAME'], 'ID' => $id, 'MODEL' => $arModels];
                }
                $arProps['BRAND'] = $arBrands;
            }
            if($arProps['MODEL']){
                $arModels = [];
                foreach($arProps['MODEL'] as $id=>$arModel){
                    $arModels[$id] = ['NAME' => $arModel['NAME'], 'ID' => $id];
                    foreach($arModel['PROPERTIES'] as $propCode => $arProp){
                        if($arProp['VALUE']) $arModels[$id][$propCode] = $arProp['VALUE'];
                    }
                }
                $arProps['MODEL'] = $arModels;
            }

            if(!$arProps) $obCache->AbortDataCache();
            $CACHE_MANAGER->EndTagCache();
            if($obCache->StartDataCache()) $obCache->EndDataCache($arProps);
        }

        return $arProps;

    }

    public function addCar($arFields, $arNames, $iblockId, $arCompany){
        $count = count($arCompany['PROPERTIES']['AUTO']['VALUE']) + 1;
        $el = new CIBlockElement;
        return $el->Add([
            "MODIFIED_BY"       => 1,
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID"         => $iblockId,
            "PROPERTY_VALUES"   => $arFields,
            "NAME"              => $arCompany['NAME'].' - '.$count.'('.$arNames['BRAND'][$arFields['BRAND']]['NAME'].' '.$arNames['MODEL'][$arFields['MODEL']]['NAME'].')',
            "ACTIVE"            => "Y",
        ]);
    }

    public function attachCarToCompany($companyId, $iblockId, $carId){

        $res = CIBlockElement::GetList([], ['IBLOCK_ID'=>$iblockId,'ID'=>$companyId], false, false, ['IBLOCK_ID', 'ID', 'PROPERTY_AUTO']);
        if($obj=$res->GetNextElement(true, false)){
            $propAuto = $obj->GetProperty('AUTO')['VALUE'];
        }
        if(!is_array($propAuto)) $propAuto = [];
        if(!in_array($carId, $propAuto)){
            $propAuto[] = $carId;
            CIBlockElement::SetPropertyValuesEx($companyId, $iblockId, array('AUTO'=>$propAuto));
            return true;
        }
        return false;
    }

    public function getFilesName($arFiles){
        $arResult = [];

        $resFiles = CFile::GetList(array('ID'=>'ASC'), array("@ID"=>implode(',',$arFiles)));
        while($arFile = $resFiles->GetNext())
            $arResult[$arFile['ID']] = $arFile['ORIGINAL_NAME'];

        return $arResult;
    }

    public function addFile($code, $arFile, $arFilesId, $companyId, $iblockId, $maxSize = 5000, $allow = []){

        if(!$arFile) return false;
        if($arFile['size']/1024 > $maxSize) return false;
        if($allow && !in_array(pathinfo($arFile['name'], PATHINFO_EXTENSION), $allow)) return false;

        $arFiles = $arResult = [];

        if($arFilesId){
            $resFiles=CFile::GetList(array('ID'=>'ASC'),array("@ID"=>implode(',',$arFilesId)));
            while($dbFile=$resFiles->GetNext())
                $arResult[]=$dbFile;

            foreach($arResult as $arr){
                $src=$_SERVER['DOCUMENT_ROOT'].'/'.COption::GetOptionString("main","upload_dir","upload")."/".$arr["SUBDIR"]."/".$arr["FILE_NAME"];
                $arFiles[]=[
                    "name"=>$arr['ORIGINAL_NAME'],
                    "size"=>$arr['FILE_SIZE'],
                    "tmp_name"=>$src,
                    "type"=>$arr['CONTENT_TYPE']
                ];
            }
        }

        $arFiles[] = $arFile;

        CIBlockElement::SetPropertyValuesEx($companyId, $iblockId, array($code => $arFiles));
        CIBlock::clearIblockTagCache($iblockId);

    }

    public function addFiles($code, $arFiles, $companyId, $iblockId, $maxSize = 5000, $allow = []){

        if(!$arFiles) return false;

        foreach($arFiles as $key=>$arFile){
            if($arFile['size']/1024 > $maxSize) unset($arFiles[$key]);
            if($allow && !in_array(pathinfo($arFile['name'], PATHINFO_EXTENSION), $allow)) unset($arFiles[$key]);
        }

        CIBlockElement::SetPropertyValuesEx($companyId, $iblockId, array($code => $arFiles));
        CIBlock::clearIblockTagCache($iblockId);
    }

    public function deleteFile($propValId, $code, $companyId){
        $del = ["MODULE_ID" => "iblock", "del" => "Y"];
        CIBlockElement::SetPropertyValueCode($companyId, $code, Array($propValId => Array("VALUE"=>$del) ) );
        CIBlock::clearIblockTagCache(getIblockIdByCode('company'));
    }

    public function getCompanies($userId, $iblockId, $maxCount){

        $obCache = new CPHPCache();
        $cacheID = 'companies'.$userId;
        $cacheLifetime = 86400;
        $cachePath = '/anketa/companies';
        if ( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) ) {
            $arResult = $obCache->GetVars();
        }else{
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cachePath);
            $CACHE_MANAGER->RegisterTag('iblock_id_'.$iblockId);
            $CACHE_MANAGER->RegisterTag('iblock_id_'.getIblockIdByCode('auto'));

            $arResult=[];
            $arFilter=[
                'IBLOCK_ID'=>$iblockId,
                'ACTIVE'=>'Y',
                '=PROPERTY_USER'=>$userId,
            ];
            $res=CIBlockElement::GetList(["SORT"=>"ASC","ID"=>"ASC"],$arFilter,false,['nTopCount'=>$maxCount],['ID','NAME','IBLOCK_ID']);
            while($resCompany=$res->GetNextElement(true,false)){
                $arCompany=$resCompany->GetFields();
                $arCompany['PROPERTIES']=$resCompany->GetProperties();
                $arCompany['NAME'] = htmlspecialchars_decode($arCompany['NAME']);
                $arResult[]=$arCompany;
            }

            if(!$arResult) $obCache->AbortDataCache();
            $CACHE_MANAGER->EndTagCache();
            if($obCache->StartDataCache()) $obCache->EndDataCache($arResult);
        }
        return $arResult;

    }

    public function getCarsArr($arIds, $iblockId, $companyId, $arNames){
        $arCars = [];
        $arFilter = [
            'IBLOCK_ID'=>$iblockId,
            'ACTIVE'=>'Y',
            'ID' => $arIds,
            '=PROPERTY_COMPANY' => $companyId
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'IBLOCK_ID']);
        while($resCar = $res->GetNextElement(true, false)){
            $arCar = $resCar->GetFields();
            $arCar['PROPERTIES'] = $resCar->GetProperties();
            if($arCar['PROPERTIES']['BRAND']['VALUE']) $arCar['PROPERTIES']['BRAND']['DISPLAY_VALUE'] = $arNames['BRAND'][$arCar['PROPERTIES']['BRAND']['VALUE']]['NAME'];
            if($arCar['PROPERTIES']['MODEL']['VALUE']) $arCar['PROPERTIES']['MODEL']['DISPLAY_VALUE'] = $arNames['MODEL'][$arCar['PROPERTIES']['MODEL']['VALUE']]['NAME'];
            $arCars[$arCar['ID']] = $arCar;
        }
        return $arCars;
    }

    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;

        if($_REQUEST['status']){

            $status = htmlspecialcharsbx($_REQUEST['status']);
            $arResult = self::checkApiResponse($status);

            $APPLICATION->RestartBuffer();

            $this->includeComponentTemplate($arResult['TEMPLATE']);

            CMain::FinalActions();

            die();

        }

        if($USER->IsAuthorized()){

            $userId = $USER->GetID();
            $this->arResult['SHOW_STEPS'] = true;

            if($_REQUEST['action'] == 'auto'){
                $template = 'step-3';

                if($_REQUEST['COMPANY']){
                    $this->arResult['SHOW_STEPS'] = false;
                    $template = 'step-3-success';
                    $arCompany = self::checkCompanyId((int)$_REQUEST['COMPANY'], $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                }else{
                    $arCompany = self::getCompanyId($userId, $this->arParams['IBLOCK_ID_COMPANY']);
                }

                $this->arResult['COMPANY'] = self::setCompanyArray($arCompany);
                $this->arResult['FORM'] = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);

                if(!$arCompany) LocalRedirect('/personal/');

                if($_REQUEST['apply'] == 'Y'){

                    if(!$_REQUEST['COMPANY']) $_REQUEST['COMPANY'] = $arCompany['ID'];
                    $arForm = self::clearRequestFields($_REQUEST, ['COMPANY', 'BRAND', 'MODEL', 'TIME', 'FIRST_PAYMENT']);
                    $checkRequest = self::checkRequestFields($arForm);

                    if($checkRequest !== true){
                        $this->arResult['ERROR'] = $checkRequest;
                    }else{

                        if(count($arCompany['PROPERTIES']['AUTO']['VALUE']) + 1 > $this->arParams['MAX_COUNT_AUTO']){
                            $this->arResult['ERROR'][] = 'К Компании привязано максимальное количество автомобилей.';
                        }else{

                            $carId = self::addCar($arForm, $this->arResult['FORM'], $this->arParams['IBLOCK_ID_AUTO'], $arCompany);
                            if($carId) {
                                if(self::attachCarToCompany($arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $carId)){

                                    $arFields = [
                                        'ID'=>$this->arResult['COMPANY']['ID'],
                                        'NAME'=>$this->arResult['COMPANY']['NAME'],
                                        'INN'=>$this->arResult['COMPANY']['INN'],
                                        'LEAD'=>$this->arResult['COMPANY']['LEAD'],
                                        'CAR_ID'=>$carId,
                                        'BRAND'=>$this->arResult['FORM']['BRAND'][$arForm['BRAND']]['NAME'],
                                        'MODEL'=>$this->arResult['FORM']['MODEL'][$arForm['MODEL']]['NAME'],
                                        'TIME'=>$this->arResult['FORM']['TIME'][$arForm['TIME']],
                                        'FIRST_PAYMENT'=>$arForm['FIRST_PAYMENT'],
                                    ];
                                    CEvent::Send('ADD_CAR_TO_COMPANY', SITE_ID, $arFields);
                                    unset($arFields);

                                    if($_REQUEST['AJAX'] === 'Y'){

                                        $APPLICATION->RestartBuffer();

                                        $this->includeComponentTemplate($template);

                                        CMain::FinalActions();

                                        die();

                                    }else{
                                        $this->includeComponentTemplate($template);
                                    }

                                }else{
                                    $this->arResult['ERROR'][] = 'Не удалось добавить автомобиль.';
                                }
                            }else{
                                $this->arResult['ERROR'][] = 'Не удалось добавить автомобиль.';
                            }
                            if($this->arResult['ERROR']){

                                $this->arResult['COMPANY'] = self::setCompanyArray($arCompany);
                                $this->arResult['FORM'] = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);

                                if($_REQUEST['AJAX'] === 'Y'){

                                    $APPLICATION->RestartBuffer();

                                    $this->includeComponentTemplate('step-2');

                                    CMain::FinalActions();

                                    die();

                                }else{
                                    $this->includeComponentTemplate('step-2');
                                }
                            }
                        }

                    }

                    if($this->arResult['ERROR']){

                        $this->arResult['COMPANY'] = self::setCompanyArray($arCompany);
                        $this->arResult['FORM'] = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);

                        if($_REQUEST['AJAX'] === 'Y'){

                            $APPLICATION->RestartBuffer();

                            $this->includeComponentTemplate('step-2');

                            CMain::FinalActions();

                            die();

                        }else{
                            $this->includeComponentTemplate('step-2');
                        }

                    }

                }else{

                    $this->includeComponentTemplate('step-2');

                }

            }elseif($_REQUEST['action'] == 'files'){

                if($_REQUEST['COMPANY']){
                    $this->arResult['SHOW_STEPS'] = false;
                    $arCompany = self::checkCompanyId((int)$_REQUEST['COMPANY'], $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                }else{
                    $arCompany = self::getCompanyId($userId, $this->arParams['IBLOCK_ID_COMPANY']);
                }

                if(!$arCompany) LocalRedirect('/personal/');

                if(($_FILES['FILES'] || $_FILES['FILES_1']) && $arCompany){
                    $arCode = ['FILES', 'FILES_1'];
                    $update = false;
                    foreach($arCode as $code){
                        if(count($arCompany['PROPERTIES'][$code]['VALUE']) < 5){
                            self::addFile($code, $_FILES[$code], $arCompany['PROPERTIES'][$code]['VALUE'], $arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $this->arParams['MAX_SIZE_FILES'], $this->arParams['ALLOWED_FORMAT']);
                            $update = true;
                        }
                    }
                    if($update) $arCompany = self::checkCompanyId($arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                }

                if((int)$_REQUEST['DELETE_FILES'] || (int)$_REQUEST['DELETE_FILES_1']){
                    if((int)$_REQUEST['DELETE_FILES']){
                        self::deleteFile((int)$_REQUEST['DELETE_FILES'],'FILES', $arCompany['ID']);
                    }else{
                        self::deleteFile((int)$_REQUEST['DELETE_FILES_1'],'FILES_1', $arCompany['ID']);
                    }
                    $arCompany = self::checkCompanyId($arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                }

                $this->arResult['COMPANY'] = self::setCompanyArray($arCompany);
                $this->arResult['FORM'] = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);

                if($this->arResult['COMPANY']['FILES']['VALUE']){
                    $this->arResult['COMPANY']['FILES']['DISPLAY_VALUE'] = self::getFilesName($this->arResult['COMPANY']['FILES']['VALUE']);
                }

                if($this->arResult['COMPANY']['FILES_1']['VALUE']){
                    $this->arResult['COMPANY']['FILES_1']['DISPLAY_VALUE'] = self::getFilesName($this->arResult['COMPANY']['FILES_1']['VALUE']);
                }

                if($_REQUEST['apply'] == 'Y'){

                    if($_REQUEST['UPDATE'] == "Y" && ($this->arResult['COMPANY']['FILES']['VALUE'] || $this->arResult['COMPANY']['FILES_1']['VALUE'])){

                        $arFiles = array_merge((array)$this->arResult['COMPANY']['FILES']['VALUE'],(array)$this->arResult['COMPANY']['FILES_1']['VALUE']);
                        $arFields = ['ID'=>$this->arResult['COMPANY']['ID'],'NAME'=>$this->arResult['COMPANY']['NAME'],'INN'=>$this->arResult['COMPANY']['INN'],'LEAD'=>$this->arResult['COMPANY']['LEAD']];
                        CEvent::Send('ADD_FILES_TO_COMPANY', SITE_ID, $arFields, 'Y', "", $arFiles);
                        unset($arFields);

                    }

                    if($_REQUEST['AJAX'] == 'Y'){

                        $APPLICATION->RestartBuffer();

                        $this->includeComponentTemplate('step-3-success');

                        CMain::FinalActions();

                        die();

                    }else{
                        $this->includeComponentTemplate();
                    }

                }else{

                    if($_REQUEST['AJAX'] == 'Y'){

                        $APPLICATION->RestartBuffer();

                        $this->arResult['UPDATE'] = true;
                        $this->includeComponentTemplate('step-3');

                        CMain::FinalActions();

                        die();

                    }else{
                        $this->includeComponentTemplate('step-3');
                    }

                }

            }elseif($_REQUEST['action'] == 'company'){

                $this->arResult['FORM'] = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);

                if($_REQUEST['apply'] == 'Y'){

                    $count = self::getUserCompaniesCount($userId, $this->arParams['IBLOCK_ID_COMPANY']);
                    if($count < $this->arParams['MAX_COUNT_COMPANIES']){

                        $arForm = self::clearRequestFields($_REQUEST, ['org_name','inn']);
                        $checkRequest = self::checkRequestFields($arForm);

                        $arUser = self::getUserArById($userId);
                        $arForm = array_merge((array)$arForm, (array)$arUser);

                        if($checkRequest !== true){
                            $this->arResult['ERROR'] = $checkRequest;
                        }else{

                            $arCookies = apiAuthorize();
                            $arResponse = [];

                            if($arCookies){

                                $arResponse = sendApiPredscoring($arCookies,$arForm);

                            }

                            $idCompany = self::addCompany($arForm, $this->arParams['IBLOCK_ID_COMPANY'], $userId, $arResponse);

                            $_REQUEST['COMPANY'] = $idCompany;
                            $arForm = self::clearRequestFields($_REQUEST, ['COMPANY', 'BRAND', 'MODEL', 'TIME', 'FIRST_PAYMENT']);
                            $arCompany = self::checkCompanyId($idCompany, $this->arParams['IBLOCK_ID_COMPANY'], $userId);

                            $arFields = ['ID'=>$arCompany['ID'],'NAME'=>$arCompany['NAME'],'INN'=>$arCompany['PROPERTIES']['INN']['VALUE'],'LEAD'=>$arCompany['PROPERTIES']['LEAD']['VALUE']];
                            CEvent::Send('ADD_COMPANY', SITE_ID, $arFields);

                            if(self::checkRequestFields($arForm) === true){
                                $carId = self::addCar($arForm, $this->arResult['FORM'], $this->arParams['IBLOCK_ID_AUTO'], $arCompany);
                                if($carId) {
                                    if(self::attachCarToCompany($arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $carId)){

                                        $arFields = [
                                            'ID'=>$arCompany['ID'],
                                            'NAME'=>$arCompany['NAME'],
                                            'INN'=>$arCompany['PROPERTIES']['INN']['VALUE'],
                                            'LEAD'=>$arCompany['PROPERTIES']['LEAD']['VALUE'],
                                            'CAR_ID'=>$carId,
                                            'BRAND'=>$this->arResult['FORM']['BRAND'][$arForm['BRAND']]['NAME'],
                                            'MODEL'=>$this->arResult['FORM']['MODEL'][$arForm['MODEL']]['NAME'],
                                            'TIME'=>$this->arResult['FORM']['TIME'][$arForm['TIME']],
                                            'FIRST_PAYMENT'=>$arForm['FIRST_PAYMENT'],
                                        ];
                                        CEvent::Send('ADD_CAR_TO_COMPANY', SITE_ID, $arFields);
                                        unset($arFields);

                                    }
                                }

                            }
                            if(($_FILES['FILES-0'] || $_FILES['FILES_1-0']) && $arCompany){
                                $arCode = ['FILES', 'FILES_1'];
                                foreach($arCode as $code){
                                    $arFiles = [];
                                    for($i=0;$i<=$this->arParams['MAX_COUNT_FILES'];$i++){
                                        if(!$_FILES[$code.'-'.$i]) break;
                                        $arFiles[] = $_FILES[$code.'-'.$i];
                                    }
                                    if($arFiles) self::addFiles($code, $arFiles, $arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $this->arParams['MAX_SIZE_FILES'], $this->arParams['ALLOWED_FORMAT']);
                                }

                                $arCompany = self::checkCompanyId($arCompany['ID'], $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                                $arFiles = array_merge((array)$arCompany['PROPERTIES']['FILES']['VALUE'],(array)$arCompany['PROPERTIES']['FILES_1']['VALUE']);
                                $arFields = ['ID'=>$arCompany['ID'],'NAME'=>$arCompany['NAME'],'INN'=>$arCompany['PROPERTIES']['INN']['VALUE'],'LEAD'=>$arCompany['PROPERTIES']['LEAD']['VALUE']];
                                CEvent::Send('ADD_FILES_TO_COMPANY', SITE_ID, $arFields, 'Y', "", $arFiles);
                                unset($arFields);

                            }

                            if($_REQUEST['AJAX'] === 'Y'){
                                $APPLICATION->RestartBuffer();

                                $this->includeComponentTemplate('step-3-success');

                                CMain::FinalActions();

                                die();
                            }else{
                                $this->includeComponentTemplate();
                            }

                        }

                        if($_REQUEST['AJAX'] === 'Y'){
                            $APPLICATION->RestartBuffer();

                            $this->includeComponentTemplate('step-all');

                            CMain::FinalActions();

                            die();
                        }else{
                            $this->includeComponentTemplate('step-all');
                        }

                    }else{

                        $this->arResult['ERROR'][] = 'У вас максимальное количество компаний.';
                        $APPLICATION->RestartBuffer();

                        $this->includeComponentTemplate('step-all');

                        CMain::FinalActions();

                        die();

                    }
                }else{
                    $this->includeComponentTemplate('step-all');
                }

            }elseif($_REQUEST['id']){

                $arCompany = self::checkCompanyId((int)$_REQUEST['id'],$this->arParams['IBLOCK_ID_COMPANY'],$userId);
                if(!$arCompany) LocalRedirect('/personal/');

                if($arCompany['PROPERTIES']['FILES']['VALUE']){
                    $arCompany['PROPERTIES']['FILES']['DISPLAY_VALUE'] = self::getFilesName($arCompany['PROPERTIES']['FILES']['VALUE']);
                }
                if($arCompany['PROPERTIES']['FILES_1']['VALUE']){
                    $arCompany['PROPERTIES']['FILES_1']['DISPLAY_VALUE'] = self::getFilesName($arCompany['PROPERTIES']['FILES_1']['VALUE']);
                }
                if($arCompany['PROPERTIES']['AUTO']['VALUE']){
                    $arNames = self::getFormOptions($this->arParams['IBLOCK_ID_AUTO']);
                    $this->arResult['AUTO'] = self::getCarsArr($arCompany['PROPERTIES']['AUTO']['VALUE'], $this->arParams['IBLOCK_ID_AUTO'], $arCompany['ID'], $arNames);
                }
                $arCompany['PERCENT'] = self::getPercent(!empty($arCompany['PROPERTIES']['AUTO']['VALUE']),!empty($arCompany['PROPERTIES']['FILES']['VALUE']),!empty($arCompany['PROPERTIES']['FILES_1']['VALUE']));

                $this->arResult['COMPANY'] = $arCompany;

                $this->includeComponentTemplate('detail');
            }else{

                $arCompanies = self::getCompanies($userId, $this->arParams['IBLOCK_ID_COMPANY'], $this->arParams['MAX_COUNT_COMPANIES']);

                foreach($arCompanies as $key=>$arComp){
                    $arCompanies[$key]['PERCENT'] = self::getPercent(!empty($arComp['PROPERTIES']['AUTO']['VALUE']),!empty($arComp['PROPERTIES']['FILES']['VALUE']),!empty($arComp['PROPERTIES']['FILES_1']['VALUE']));
                }
                $arUser = self::getUserArById($userId);

                if($_REQUEST['action'] == 'user'){

                    $arForm = self::clearRequestFields($_REQUEST, ['name','surname', 'second_name', 'USER_ID', 'date']);

                    if($arForm['USER_ID'] == $userId){
                        unset($arForm['USER_ID']);
                        $checkRequest = self::checkRequestFields($arForm);

                        if($checkRequest !== true){
                            $this->arResult['ERROR'] = $checkRequest;
                        }else{

                            if(self::changeUser($userId, $arForm)){

                                $arUser = self::getUserArById($userId);

                            }

                        }

                    }

                }
                $this->arResult['COMPANIES'] = $arCompanies;
                $this->arResult['USER'] = $arUser;

                if($_REQUEST['AJAX'] === 'Y'){
                    $APPLICATION->RestartBuffer();

                    $this->includeComponentTemplate();

                    CMain::FinalActions();

                    die();
                }else{
                    $this->includeComponentTemplate();
                }

            }

        }else{

            if($_REQUEST['action'] == 'registration'){

                $this->arResult['REQUEST'] = $_REQUEST;

                if(!self::checkGoogleRecaptcha($_REQUEST['g-recaptcha-response'])){
                    $this->arResult['ERROR'][] = 'Подтвердите, что вы не робот.';

                    $APPLICATION->RestartBuffer();

                    $this->includeComponentTemplate('step-1');

                    CMain::FinalActions();

                    die();
                }

                $phone =  $_REQUEST['phone'] = self::checkPhone($_REQUEST['phone']);
                $arForm = self::clearRequestFields($_REQUEST, $this->arParams['REQUEST_FIELDS']);

                if($phone){
                    $arUser = self::getUserByPhone($phone);
                    if(!$arUser) $arUser = self::getUserByEmail($arForm['email']);
                    if($arUser){

                        if($_REQUEST['AJAX'] === 'Y'){

                            $APPLICATION->RestartBuffer();

                            $this->includeComponentTemplate('gotologin');

                            CMain::FinalActions();

                            die();

                        }else{
                            LocalRedirect('/auth/');
                            die();
                        }

                    }
                }

                $checkRequest = self::checkRequestFields($arForm);

                if($checkRequest !== true){
                    $this->arResult['ERROR'] = $checkRequest;
                }else{

                    $arCookies = apiAuthorize();
                    $arResponse = [];

                    if($arCookies){

                        $arResponse = sendApiPredscoring($arCookies, $arForm);

                        if($arResponse){

                            $arResult = self::checkApiResponse($arResponse['status']);

                        }/*else{
                            $this->arResult['ERROR'][] = 'Ошибка при обработке данных.';
                        }*/

                    }/*else{
                        $this->arResult['ERROR'][] = 'Ошибка при подключении к серверу.';
                    }*/


                    if(!$arResult['USER_EXIST']){

                        $userId = self::registrationUser($arForm);

                        $idCompany = self::addCompany($arForm, $this->arParams['IBLOCK_ID_COMPANY'], $userId, $arResponse);

                        $arCompany = self::checkCompanyId($idCompany, $this->arParams['IBLOCK_ID_COMPANY'], $userId);
                        $arFields = ['ID'=>$arCompany['ID'],'NAME'=>$arCompany['NAME'],'INN'=>$arCompany['PROPERTIES']['INN']['VALUE'],'LEAD'=>$arCompany['PROPERTIES']['LEAD']['VALUE']];
                        CEvent::Send('ADD_COMPANY', SITE_ID, $arFields);

                    }

                    if(!$userId){
                        $this->arResult['ERROR'][] = 'Ошибка при создании пользователя.';
                    }

                    if($userId && !$arResult['TEMPLATE']){
                        $arResult['TEMPLATE'] = 'fail';
                        //$arResult['TEMPLATE'] = 'success';
                    }


                    if($this->arResult['ERROR']){

                        if($_REQUEST["AJAX"] === "Y"){

                            $APPLICATION->RestartBuffer();

                            $this->includeComponentTemplate($arResult['TEMPLATE']);

                            CMain::FinalActions();

                            die();

                        }else{
                            $this->includeComponentTemplate($arResult['TEMPLATE']);
                        }

                    }else{

                        $this->arResult['LEAD'] = $arResponse['lead'];

                        if($_REQUEST["AJAX"] === "Y"){

                            $APPLICATION->RestartBuffer();

                            $this->includeComponentTemplate($arResult['TEMPLATE']);

                            CMain::FinalActions();

                            die();

                        }else{
                            $this->includeComponentTemplate($arResult['TEMPLATE']);
                        }

                    }

                }

                if($_REQUEST["AJAX"] === "Y"){

                    $APPLICATION->RestartBuffer();

                    $this->includeComponentTemplate('step-1');

                    CMain::FinalActions();

                    die();

                }else{
                    $this->includeComponentTemplate('step-1');
                }

            }else{

                $this->includeComponentTemplate('step-1');

            }

        }

    }

}
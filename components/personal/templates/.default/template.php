<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<section class="section companies__section">
    <div class="container">
        <h1 class="title">Информация о компаниях проверенных через предскоринг</h1>
        <div class="companies-count">Добавлено компаний: <?=count($arResult['COMPANIES'])?> / <?=$arParams['MAX_COUNT_COMPANIES']?></div>
        <div class="companies">
            <?foreach($arResult['COMPANIES'] as $arCompany){?>
                <?$maxAuto = true;
                if(count($arCompany['PROPERTIES']['AUTO']['VALUE']) < $arParams['MAX_COUNT_AUTO']){
                    $maxAuto = false;
                }?>
                <?switch($arCompany['PROPERTIES']['STATUS']['VALUE']){
                    case 'positive':case 'current':
                        $class = ' companies__item_success';
                        $result = '<div class="companies__success"><svg><use xlink:href="#icon__success" /></svg>Одобрено</div>';
                        break;
                    case 'negative':
                        $class = ' companies__item_error';
                        $result = '<div class="companies__error"><svg><use xlink:href="#icon_error" /></svg>Отклонено</div>';
                        break;
                    default:
                        $class = '';
                        if($maxAuto){
                            $url = '?action=files&COMPANY='.$arCompany["ID"];
                        }else{
                            $url = '?action=auto&COMPANY='.$arCompany["ID"];
                        }
                        $result = '<a class="companies__btn button button_filled" href="'.$url.'">+ Добавить данные</a>';
                        break;
                }?>
                <div class="companies__item<?=$class?>" data-url="?id=<?=$arCompany['ID']?>">
                    <div class="companies__left">
                        <div class="companies__diagram">
                            <div class="steps__info">
                                <div class="steps__info-icon">!</div>
                                <div class="steps__info-popup">Шансы на одобрение заявки по объему предоставленных данных</div>
                            </div>
                            <?if($arCompany['PERCENT']){?>
                                <div class="progress-circle js-progress"><?=$arCompany['PERCENT']?>%</div>
                            <?}?>
                        </div>
                        <div class="companies__info">
                            <div class="companies__name"><?=$arCompany['NAME']?></div>
                            <?if($arCompany['PROPERTIES']['INN']['VALUE']){?>
                                <div class="companies__inn">ИНН: <?=$arCompany['PROPERTIES']['INN']['VALUE']?></div>
                            <?}?>
                        </div>
                    </div>
                    <div class="companies__right">
                        <div class="companies__field">
                            <div class="companies__field-name">Автомобили</div>
                            <div class="companies__field-value">
                                <?=($arCompany['PROPERTIES']['AUTO']['VALUE']?count($arCompany['PROPERTIES']['AUTO']['VALUE']):0)?> шт.
                                <?if(!$maxAuto){?> <a href="?action=auto&COMPANY=<?=$arCompany['ID']?>">+</a><?}?>
                            </div>
                        </div>
                        <div class="companies__field">
                            <div class="companies__field-name">Документы собственника</div>
                            <div class="companies__field-value">
                                <?=(($arCompany['PROPERTIES']['FILES']['VALUE']?count($arCompany['PROPERTIES']['FILES']['VALUE']):0)+($arCompany['PROPERTIES']['FILES_1']['VALUE']?count($arCompany['PROPERTIES']['FILES_1']['VALUE']):0))?> шт.
                                <a href="?action=files&COMPANY=<?=$arCompany['ID']?>">+</a>
                            </div>
                        </div>
                        <div class="companies__btn-wrap">
                            <?=$result?>
                        </div>
                    </div>
                </div>
            <?}?>
            <?if(count($arResult['COMPANIES']) >= $arParams['MAX_COUNT_COMPANIES']){?>
                <div class="companies__max">Вы добавили максимальное количество компаний!</div>
            <?}else{?>
                <a class="companies__btn button button_filled" href="?action=company">Добавить организацию</a>
            <?}?>
        </div>
    </div>
</section>
<?if($arResult['USER']){?>
<section class="section steps__section">
    <div class="container">
        <div class="steps__wrap">
            <div class="steps__form-wrap companies__form">
                <div class="steps__form-head companies__form-head">
                    <h2 class="steps__form-title">Персональная информация заявителя</h2>

                    <div class="companies__change-info js-change-info">Изменить данные</div>
                </div>
                <?if($arResult['ERROR']){?>
                    <div class="steps__form-row steps__form-row_errors">
                        <?=implode('<br>',$arResult['ERROR'])?>
                    </div>
                <?}?>
                <form action="<?=$APPLICATION->GetCurPage()?>" class="steps__form">
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Фамилия" name="surname" value="<?=$arResult['USER']['LAST_NAME']?>">
                        </label>
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Имя" name="name" value="<?=$arResult['USER']['NAME']?>">
                        </label>
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Отчество" name="second_name" value="<?=$arResult['USER']['SECOND_NAME']?>">
                        </label>
                    </div>
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal steps__form-field_date">
                            <input type="text" class="steps__form-input js-validate js-datepicker"
                                   value="<?=$arResult['USER']['PERSONAL_BIRTHDAY']?>" placeholder="дд.мм.гггг" name="date" onkeypress="validateDate(event)" maxlength="10">
                            <svg>
                                <use xlink:href="#ico_calendar" />
                            </svg>
                        </label>
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal">
                            <input type="tel" class="steps__form-input" value="<?=$arResult['USER']['PERSONAL_MOBILE']?>" placeholder="+7 (___) ___-__-__" name="PHONE">
                        </label>
                        <label class="steps__form-field steps__form-field_disable steps__form-field_normal">
                            <input type="email" class="steps__form-input js-validate" value="<?=($arResult['USER']['EMAIL']?$arResult['USER']['EMAIL']:$arResult['USER']['LOGIN'])?>"
                                   placeholder="E-mail" name="email">
                        </label>
                    </div>
                    <div class="steps__form-row">
                        <input hidden type="hidden" name="USER_ID" value="<?=$arResult['USER']['ID']?>">
                        <input hidden type="hidden" name="action" value="user">
                        <button class="steps__save-btn button button_filled js-companies-save-btn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?}?>
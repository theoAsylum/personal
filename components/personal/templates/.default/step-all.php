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

use \Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/module/suggestions/suggestions.min.css");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/module/suggestions/jquery.suggestions.min.js");
Asset::getInstance()->addJs($templateFolder.'/step-all.js');
?>
<section class="section steps__section">
    <div class="container">
        <h1 class="title">Добавить новую организацию на проверку</h1>
        <div class="steps__wrap">
            <div class="steps__text-part steps__text-part_step-1">
                <div class="steps__text">Заполните данные для оформления заявки
                </div>
            </div>
            <div class="steps__form-wrap">
                <div class="steps__form-head">
                    <div class="steps__form-title">Информация о компании</div>
                </div>
                <?if($arResult['ERROR']){?>
                    <div class="steps__form-row steps__form-row_errors">
                        <?=implode('<br>',$arResult['ERROR'])?>
                    </div>
                <?}?>
                <form action="<?=$APPLICATION->GetCurPage()?>" class="steps__form" method="post">
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="ИНН Организации" name="inn"<?if($arResult['REQUEST']['inn']){?> value="<?=$arResult['REQUEST']['inn']?>"<?}?>>
                        </label>
                        <label class="steps__form-field steps__form-field_name">
                            <input type="text" class="steps__form-input js-validate" placeholder="Название организации"
                                   name="org_name"<?if($arResult['REQUEST']['org_name']){?> value="<?=$arResult['REQUEST']['org_name']?>"<?}?>>
                        </label>
                    </div>

                    <div class="steps__form-head">
                        <div class="steps__form-title">Данные об автомобиле и первом платеже</div>
                    </div>
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_select">
                            <select class="steps__form-select js-step-select" data-select-placeholder="Марка автомобиля" data-search-placeholder="Начните вводить марку" name="BRAND">
                                <option></option>
                                <?foreach($arResult['FORM']['BRAND'] as $brandId => $arBrand){?>
                                    <option value="<?=$brandId?>" data-model="<?=implode(',',$arBrand['MODEL'])?>"><?=$arBrand['NAME']?></option>
                                <?}?>
                            </select>
                        </label>
                        <label class="steps__form-field steps__form-field_select">
                            <select class="steps__form-select js-step-select" data-select-placeholder="Модель автомобиля" data-search-placeholder="Начните вводить модель" name="MODEL">
                                <option></option>
                                <?foreach($arResult['FORM']['MODEL'] as $modelId => $arModel){?>
                                    <?$arPayment = [];
                                    foreach($arModel as $code => $val){
                                        if(strpos($code,'MONTH') !== false){
                                            $arPayment[] = $val;
                                        }
                                    }?>
                                    <option value="<?=$modelId?>" data-brand="<?=$arModel['BRAND']?>" data-payment="<?=implode(',',$arPayment)?>"><?=$arModel['NAME']?></option>
                                <?}?>
                            </select>
                        </label>
                        <div class="steps__form-field steps__form-field_reset">
                            <button class="button button_bordered js-reset-btn">Сбросить</button>
                        </div>
                    </div>

                        <div class="steps__form-row">
                            <div class="steps__form-h2">Желаемый срок лизинга</div>
                        </div>
                        <div class="steps__form-row">
                            <?$i=0?>
                            <?foreach($arResult['FORM']['TIME'] as $timeId => $timeName){?>
                                <label class="steps__form-field steps__form-field_radio">
                                    <input type="radio" name="TIME" class="steps__form-radio"<?if($i==0){?> checked="checked" <?}?>value="<?=$timeId?>" data-num="<?=$i?>"><span><?=$timeName?></span>
                                </label>
                                <?$i++?>
                            <?}?>
                        </div>

                    <?$price = 1000000;
                    $min_pay = $price/4?>
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_range">
                            <span class="steps__form-range-text-part">
                                <span class="steps__form-range-title">
                                    Размер первого платежа
                                </span>
                                <span class="steps__form-range-price js-step-range-price">
                                    <?=number_format($min_pay, 0, '', ' ' )?> ₽
                                </span>
                            </span>
                            <input class="steps__form-range js-step-range" type="text">
                            <input hidden type="hidden" name="FIRST_PAYMENT" value="<?=$min_pay?>">
                            <input hidden type="hidden" name="price" value="<?=$price?>">
                        </label>
                    </div>

                    <div class="steps__form-head">
                        <div class="steps__form-title">Информация о директоре и собственниках компании</div>
                    </div>
                    <div class="steps__form-row">
                        <div class="steps__form-h2">Прикрепите копию паспорта генерального директора (2й и 3й развороты)</div>
                    </div>
                    <div class="steps__form-row steps__form-row_upload">
                        <div class="steps__form-field steps__form-field_file">
                            <label class="steps__form-file">
                                <input type="file" name="FILES" multiple> <span>+ Загрузить файл</span>
                            </label>
                            <span class="steps__form-file-text">(doc, rtf, jpg, png, pdf весом не более 5 Мб)</span>
                        </div>
                    </div>
                    <div class="steps__form-row">
                        <div class="steps__form-h2">Прикрепите данные бенефициаров организации, если таковые имеются
                            <br>(Прикрепите данные об учредителях – физических лица, владеющих долей в
                            уставном капитале более 25%, и бенефициарных владельцах.)
                        </div>
                    </div>
                    <div class="steps__form-row steps__form-row_upload">
                        <div class="steps__form-field steps__form-field_file">
                            <label class="steps__form-file">
                                <input type="file" name="FILES_1" multiple><span>+ Загрузить файл</span>
                            </label>
                            <span class="steps__form-file-text">(doc, rtf, jpg, png, pdf весом не более 5 Мб)</span>
                        </div>
                    </div>
                    <div class="steps__form-row steps__form-row_footer">
                        <div class="steps__form-field steps__form-field_skip">
                            <a class="steps__form-skip" href="/personal/">
                                Вернуться назад
                            </a>

                        </div>
                        <div class="steps__form-field steps__form-field_next">
                            <input hidden type="hidden" name="apply" value="Y">
                            <input hidden type="hidden" name="action" value="company">
                            <button class="button button_filled" type="submit">
                                Отправить на проверку
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
<div class="modal steps steps_waiting" id="steps-load">
    <div class="modal__close" data-fancybox-close>
        <svg>
            <use xlink:href="#icon_close" />
        </svg>
    </div>
    <form action="#" class="modal__wrap">
        <div class="modal__title">Спасибо за ваше обращение!</div>
        <div class="modal__text">На рассмотрение заявки требуется около 30-45 секунд.</div>
        <div class="modal__loader">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/style/svg-loader.svg" alt="">
        </div>
        <div class="modal__text">Мы анализируем предоставленные данные, дождитесь пожалуйста результатов
            проверки предоставленной информации.</div>
        <div class="modal__text">Результат по заявке так же будет доступен в вашем <a href="/auth/">Личном кабинете.</a>

        </div>
        <?if($arResult['LEAD']){?><input hidden type="hidden" name="LEAD" value="<?=$arResult['LEAD']?>"><?}?>
    </form>
</div>
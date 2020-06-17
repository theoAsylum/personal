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
Asset::getInstance()->addJs($templateFolder.'/step-2.js');
?>
<section class="section steps__section">
    <div class="container">
        <h1 class="title">Online анкета пре-скоринга по лизингу</h1>
        <div class="steps__wrap">
            <div class="steps__text-part steps__text-part_step-2">
                <div class="steps__text">Предоставление данных об автомобиле повышают ваши шансы на одобрение заявки
                    на финансирование лизинга. Укажите марку и модель вашего автомобиля, выберите
                    желаемый срок лизинга, а так же первый взнос.
                </div>
                <div class="step-diagram-info">
                    <div class="step-diagram-info__left">
                        <div class="step-diagram-info__title"><?=$arResult['COMPANY']['NAME']?></div>
                        <?if(!$arResult['COMPANY']['AUTO']){?>
                            <div class="step-diagram-info__text">
                                <span class="step-diagram-info__degree">+<?=$arParams['PERCENT_FOR_CAR']?>%</span> к одобрению,
                                при предоставлении
                                данных об автомобиле
                            </div>
                        <?}?>
                    </div>
                    <div class="step-diagram-info__right">
                        <div class="steps__info">
                            <div class="steps__info-icon">!</div>
                            <div class="steps__info-popup">Шансы на одобрение заявки по объему предоставленных данных</div>
                        </div>
                        <div class="progress-circle js-progress"><?=$arResult['COMPANY']['PERCENT']?>%</div>
                    </div>
                </div>
            </div>
            <div class="steps__form-wrap">
                <div class="steps__form-head">
                    <div class="steps__form-title">Данные об автомобиле и первом платеже</div>
                </div>
                <?if($arResult['ERROR']){?>
                    <div class="steps__form-row steps__form-row_errors">
                        <?=implode('<br>',$arResult['ERROR'])?>
                    </div>
                <?}?>
                <form action="<?=$APPLICATION->GetCurPage()?>" class="steps__form">
                    <?if($arResult['FORM']['BRAND']){?>
                        <div class="steps__form-row">
                            <label class="steps__form-field steps__form-field_select">
                                <select class="steps__form-select js-step-select" data-select-placeholder="Марка автомобиля" data-search-placeholder="Начните вводить марку" name="BRAND" required>
                                    <option></option>
                                    <?foreach($arResult['FORM']['BRAND'] as $brandId => $arBrand){?>
                                        <option value="<?=$brandId?>" data-model="<?=implode(',',$arBrand['MODEL'])?>"><?=$arBrand['NAME']?></option>
                                    <?}?>
                                </select>
                            </label>
                            <label class="steps__form-field steps__form-field_select">
                                <select class="steps__form-select js-step-select" data-select-placeholder="Модель автомобиля" data-search-placeholder="Начните вводить модель" name="MODEL" required>
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
                    <?}?>
                    <?if($arResult['FORM']['TIME']){?>
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
                    <?}?>
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
                    <div class="steps__form-row steps__form-row_footer">
                        <div class="steps__form-field steps__form-field_skip">
                            <?if($arResult['SHOW_STEPS']){?>
                                <a class="steps__form-skip" href="/personal/?action=files&companyId=<?=$arResult['COMPANY']['ID']?>">
                                    Пропустить
                                </a>
                            <?}else{?>
                                <a class="steps__form-skip" href="/personal/">
                                    Вернуться назад
                                </a>
                            <?}?>

                        </div>
                        <div class="steps__form-field steps__form-field_next">
                            <input hidden type="hidden" name="action" value="auto">
                            <input hidden type="hidden" name="apply" value="Y">
                            <input hidden type="hidden" name="COMPANY" value="<?=$arResult['COMPANY']['ID']?>">
                            <button class="button button_filled" type="submit">
                                <?if($arResult['SHOW_STEPS']){?>
                                    Следующий шаг
                                <?}else{?>
                                    Отправить данные
                                <?}?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?if($arResult['SHOW_STEPS']){?>
            <div class="step-part">
                <div class="step-part__item active">
                    <div class="step-part__number">1</div>
                    <div class="step-part__text">
                        <p>Регистрация и ввод информации о компании</p>
                        <svg>
                            <use xlink:href="#icon_step-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="step-part__item active">
                    <div class="step-part__number">2</div>
                    <div class="step-part__text">
                        <p>Информация об автомобиле и первом взносе</p>
                        <svg>
                            <use xlink:href="#icon_step-arrow"></use>
                        </svg>
                    </div>
                </div>
                <div class="step-part__item">
                    <div class="step-part__number">3</div>
                    <div class="step-part__text">
                        <p>Информация о Генеральном директоре и собственниках</p>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
</section>
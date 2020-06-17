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
Asset::getInstance()->addJs($templateFolder.'/step-3.js');
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
                    <div class="steps__form-title">Информация о директоре и собственниках компании</div>
                </div>
                <?if($arResult['ERROR']){?>
                    <div class="steps__form-row steps__form-row_errors">
                        <?=implode('<br>',$arResult['ERROR'])?>
                    </div>
                <?}?>
                <form action="<?=$APPLICATION->GetCurPage()?>" class="steps__form">
                    <div class="steps__form-row">
                        <div class="steps__form-h2">Прикрепите копию паспорта генерального директора (2й и 3й развороты)</div>
                    </div>
                    <div class="steps__form-row steps__form-row_upload">
                        <?if($arResult['COMPANY']['FILES']['DISPLAY_VALUE']){?>
                            <?$i=0?>
                            <?foreach($arResult['COMPANY']['FILES']['DISPLAY_VALUE'] as $fileId => $fileName){?>
                                <div class="steps-upload-file__item">
                                    <div class="steps-upload-file__icon">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-icon.svg" alt="">
                                    </div>
                                    <div class="steps-upload-file__text"><?=$fileName?></div>
                                    <div class="steps-upload-file__del js-upload-file-delete" data-file-id="<?=$arResult['COMPANY']['FILES']['VALUE_FOR_DEL'][$i]?>" data-name="FILES">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-del-icon.svg" alt="">
                                    </div>
                                </div>
                                <?$i++?>
                            <?}?>
                        <?}?>
                        <?if(count($arResult['COMPANY']['FILES']['DISPLAY_VALUE']) < 5){?>
                            <div class="steps__form-field steps__form-field_file">
                                <label class="steps__form-file">
                                    <input type="file" name="FILES"> <span>+ Загрузить файл</span>
                                </label>
                                <span class="steps__form-file-text">(doc, rtf, jpg, png, pdf весом не более 5 Мб)</span>
                            </div>
                        <?}?>
                    </div>
                    <div class="steps__form-row">
                        <div class="steps__form-h2">Прикрепите данные бенефициаров организации, если таковые имеются
                            <br>(Прикрепите данные об учредителях – физических лица, владеющих долей в
                            уставном капитале более 25%, и бенефициарных владельцах.)
                        </div>
                    </div>
                    <div class="steps__form-row steps__form-row_upload">
                        <?if($arResult['COMPANY']['FILES_1']['DISPLAY_VALUE']){?>
                            <div class="steps-upload-file">
                                <?$i=0?>
                                <?foreach($arResult['COMPANY']['FILES_1']['DISPLAY_VALUE'] as $fileId => $fileName){?>
                                    <div class="steps-upload-file__item">
                                        <div class="steps-upload-file__icon">
                                            <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-icon.svg" alt="">
                                        </div>
                                        <div class="steps-upload-file__text"><?=$fileName?></div>
                                        <div class="steps-upload-file__del js-upload-file-delete" data-file-id="<?=$arResult['COMPANY']['FILES_1']['VALUE_FOR_DEL'][$i]?>" data-name="FILES_1">
                                            <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-del-icon.svg" alt="">
                                        </div>
                                    </div>
                                    <?$i++?>
                                <?}?>
                            </div>
                        <?}?>
                        <?if(count($arResult['COMPANY']['FILES_1']['DISPLAY_VALUE']) < 5){?>
                            <div class="steps__form-field steps__form-field_file">
                                <label class="steps__form-file">
                                    <input type="file" name="FILES_1"><span>+ Загрузить файл</span>
                                </label>
                                <span class="steps__form-file-text">(doc, rtf, jpg, png, pdf весом не более 5 Мб)</span>
                            </div>
                        <?}?>
                    </div>
                    <div class="steps__form-row steps__form-row_footer">
                        <div class="steps__form-field steps__form-field_skip">
                            <?if($arResult['SHOW_STEPS']){?>
                                <a class="steps__form-skip" href="/personal/">
                                    Пропустить
                                </a>
                            <?}else{?>
                                <a class="steps__form-skip" href="/personal/">
                                    Вернуться назад
                                </a>
                            <?}?>

                        </div>
                        <div class="steps__form-field steps__form-field_next">
                            <input hidden type="hidden" name="COMPANY" value="<?=$arResult['COMPANY']['ID']?>">
                            <input hidden type="hidden" name="action" value="files">
                            <?if($arResult['UPDATE']){?>
                                <input hidden type="hidden" name="UPDATE" value="Y">
                            <?}?>
                            <input hidden type="hidden" name="apply" value="Y">
                            <button class="button button_filled" type="submit">
                                <?if($arResult['SHOW_STEPS']){?>
                                    Отправить на проверку
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
                <div class="step-part__item active">
                    <div class="step-part__number">3</div>
                    <div class="step-part__text">
                        <p>Информация о Генеральном директоре и собственниках</p>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
</section>
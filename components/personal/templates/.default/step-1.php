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
Asset::getInstance()->addJs($templateFolder.'/step-1.js');
Asset::getInstance()->addJs('https://www.google.com/recaptcha/api.js?render='.GOOGLE_RECAPTCHA_PUBLIC_KEY);
?>
<section class="section steps__section">
    <div class="container">
        <h1 class="title">Online анкета пре-скоринга по лизингу</h1>
        <div class="steps__wrap">
            <div class="steps__text-part steps__text-part_step-1">
                <div class="steps__text">Заполните анкету из 3х простых шагов и получите ответ, о возможности финансирования
                    вашего юридического лица или ИП. Проверка в нашей системе скоринга занимает
                    около 10 минут
                </div>
            </div>
            <div class="steps__form-wrap">
                <div class="steps__form-head">
                    <div class="steps__form-title">Информация о компании</div>
                    <div class="steps__info">
                        <div class="steps__info-icon">!</div>
                        <div class="steps__info-popup">Шансы на одобрение заявки по объему предоставленных данных</div>
                    </div>
                </div>
                <?if($arResult['ERROR']){?>
                    <div class="steps__form-row steps__form-row_errors">
                        <?=implode('<br>',$arResult['ERROR'])?>
                    </div>
                <?}?>
                <form action="<?=$APPLICATION->GetCurPage()?>" class="steps__form" method="post">
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Фамилия" name="surname">
                        </label>
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Имя" name="name">
                        </label>
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="Отчество" name="second_name">
                        </label>
                    </div>
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_normal steps__form-field_date">
                            <input type="text" class="steps__form-input js-validate js-datepicker"
                                   placeholder="дд.мм.гггг" name="date"<?if($arResult['REQUEST']['date']){?> value="<?=$arResult['REQUEST']['date']?>"<?}?> onkeypress="validateDate(event)" maxlength="10">
                            <svg>
                                <use xlink:href="#ico_calendar"/>
                            </svg>
                        </label>
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="tel" class="steps__form-input js-validate js-masked" placeholder="+7 (___) ___-____"
                                   name="phone"<?if($arResult['REQUEST']['phone']){?> value="<?=$arResult['REQUEST']['phone']?>"<?}?>>
                        </label>
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="email" class="steps__form-input js-validate" placeholder="E-mail" name="email"<?if($arResult['REQUEST']['email']){?> value="<?=$arResult['REQUEST']['email']?>"<?}?>>
                        </label>
                    </div>
                    <div class="steps__form-row">
                        <label class="steps__form-field steps__form-field_normal">
                            <input type="text" class="steps__form-input js-validate" placeholder="ИНН Организации" name="inn"<?if($arResult['REQUEST']['inn']){?> value="<?=$arResult['REQUEST']['inn']?>"<?}?>>
                        </label>
                        <label class="steps__form-field steps__form-field_name">
                            <input type="text" class="steps__form-input js-validate" placeholder="Название организации"
                                   name="org_name"<?if($arResult['REQUEST']['org_name']){?> value="<?=$arResult['REQUEST']['org_name']?>"<?}?>>
                        </label>
                        <input hidden type="hidden" name="action" value="registration">
                        <div class="steps__form-field steps__form-field_submit">
                            <button class="button button_filled" type="submit">
                                Отправить заявку
                            </button>

                        </div>
                    </div>
                    <div class="steps__form-row">
                        <label class="step-agreement">
                            <input type="checkbox" checked="checked" name="policy"> <span class="step-agreement__checkbox"></span>
                            <span class="step-agreement__text">
                                Принимаю условия
                                <a href="#policy-modal" data-fancybox="">политики конфиденциальности</a>
                            </span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
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
            <div class="step-part__item">
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
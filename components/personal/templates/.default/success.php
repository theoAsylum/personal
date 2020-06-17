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
<div class="modal steps" id="steps-load">
    <div class="modal__close" data-fancybox-close>
        <svg>
            <use xlink:href="#icon_close" />
        </svg>
    </div>
    <form action="#" class="modal__wrap">
        <div class="modal__title">Поздравляем! Ваши данные одобрены.</div>
        <div class="modal__text">Для того, чтобы продолжить оформление лизинга, перейдите в Личный кабинет сайта</div>
        <div class="modal__button">
            <a href="https://www.com/login/" class="button button_filled" target="_blank">Перейти в личный кабинет</a>
        </div>
        <div class="modal__text">Логин и пароль для доступа отправленны вам на почту, указанную при регистрации.</div>
    </form>
</div>
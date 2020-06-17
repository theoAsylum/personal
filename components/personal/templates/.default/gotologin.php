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
        <div class="modal__title">Вы уже раннее регистрировались в системе!</div>
        <div class="modal__text">Чтобы получить информацию по вашим заявкам, войдите в Личный Кабинет используя номер телефона</div>
        <div class="modal__button"> <a href="/auth/" class="button button_filled">Войти в Личный кабинет</a>
        </div>
    </form>
</div>
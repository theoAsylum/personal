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
        <div class="modal__title">Спасибо за ожидание!</div>
        <div class="modal__text">Предоставленных вами данных недостаточно для получения положительного ответа по прескорингу</div>
        <div class="modal__button">
            <a href="/auth/" class="button button_filled">+ Добавить данные</a>
        </div>
        <div class="modal__text">Войдите в <a class="modal__link" href="/auth/">Личный кабинет</a> и добавьте информацию о себе и интересующем вас автомобиле, это увеличит шансы получения одобрения.</div>
        <div class="modal__text">Так же вы можете запросить обратный звонок - <a data-fancybox="" data-src="#header_callback" class="modal__link modal__link_dark">перезвоните мне</a></div>
    </form>
</div>
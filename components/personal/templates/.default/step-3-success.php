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
Asset::getInstance()->addJs($templateFolder.'/step-3-success.js');
?>
<div class="modal steps" id="steps-load">
    <div class="modal__close" data-fancybox-close>
        <svg>
            <use xlink:href="#icon_close" />
        </svg>
    </div>
    <form action="#" class="modal__wrap">
        <div class="modal__title">Спасибо! Информация принята.</div>
        <div class="modal__text">Наш менеджер свяжется с вами в ближайшее время.</div>
        <div class="modal__button"> <a href="/personal/" class="button button_filled">Перейти в профиль</a>
        </div>
    </form>
</div>
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
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);
?>
<?switch($arResult['COMPANY']['PROPERTIES']['STATUS']['VALUE']){
    case 'positive':case 'current':
        $status = '<div class="check-company__permission check-company__permission_success">
                            <svg><use xlink:href="#icon__success" /></svg>Одобрено
                    </div>';
        $result = '<div class="check-company__text">
                    Вам предварительно <span class="success"> одобрен</span> лизинг. В ближайшее время с вами свяжется наш менеджер.
                    </div>
                    <div class="check-company__text">
                    Так же вы можете продолжить выбор автомобиля онлайн и сделать предварительный расчёт коммерческого предложения.
                    Для этого следуйте инструкциям у вас на почте.
                    </div>';
        break;
    case 'waiting':
        $status = '<div class="check-company__permission">
                        Ожидает решения
                    </div>';
        $result = '<div class="check-company__text">
                    Мы анализируем предоставленные данные, дождитесь пожалуйста результатов проверки предоставленной информации.
                    </div>';
        break;
    case 'negative':case 'false':
        $status = '<div class="check-company__permission">
                        Отказано
                    </div>';
        $result = '<div class="check-company__text">
                    Предоставленных вами данных недостаточно для получения положительного ответа по прескорингу.
                    </div>';
        break;
        default:
        $status = '';
        $result = '<div class="check-company__text">
                    Предоставленных вами данных недостаточно для получения положительного ответа по прескорингу.
                    </div>';
        break;
}?>
<section class="section check-company__section">
    <div class="container">
        <h1 class="title"><?=$arResult['COMPANY']['NAME']?></h1>
        <div class="check-company__check-result">
            <div class="check-company__title">Результат проверки компании</div>
            <div class="check-company__sec-title">Спасибо за предоставленную информацию</div>
            <?=$result?>
            <div class="check-company__btn">
                <a data-fancybox="" data-src="#header_callback" class="button button_filled">Перезвоните мне</a>
            </div>
        </div>
        <div class="check-company__check-info">
            <div class="check-company__title">Информация о проверке компании</div>
            <div class="check-company__info">
                <div class="check-company__info-left">
                    <div class="check-company__diagram">
                        <div class="steps__info">
                            <div class="steps__info-icon">!</div>
                            <div class="steps__info-popup">Шансы на одобрение заявки по объему предоставленных данных</div>
                        </div>
                        <div class="progress-circle js-progress"><?=$arResult['COMPANY']['PERCENT']?>%</div>
                    </div>
                    <div class="check-company__info-comp">
                        <div class="check-company__name"><?=$arResult['COMPANY']['NAME']?></div>
                        <?if($arResult['COMPANY']['PROPERTIES']['INN']['VALUE']){?>
                            <div class="check-company__inn">ИНН: <?=$arResult['COMPANY']['PROPERTIES']['INN']['VALUE']?></div>
                        <?}?>
                        <?=$status?>
                    </div>
                </div>
                <?if($arResult['AUTO']){?>
                    <?foreach($arResult['AUTO'] as $arCar){?>
                        <div class="check-company__info-right">
                            <div class="check-company__field">
                                <div class="check-company__field-name">Автомобиль</div>
                                <div class="check-company__field-value"><?=$arCar['PROPERTIES']['BRAND']['DISPLAY_VALUE']?><?=($arCar['PROPERTIES']['MODEL']['DISPLAY_VALUE']?' '.$arCar['PROPERTIES']['MODEL']['DISPLAY_VALUE']:'')?></div>
                            </div>
                            <div class="check-company__field">
                                <div class="check-company__field-name">Желаемый срок лизинга</div>
                                <div class="check-company__field-value"><?=$arCar['PROPERTIES']['TIME']['VALUE']?></div>
                            </div>
                            <div class="check-company__field">
                                <div class="check-company__field-name">Размер первого платежа</div>
                                <div class="check-company__field-value"><?=number_format($arCar['PROPERTIES']['FIRST_PAYMENT']['VALUE'], 0, '', ' ' )?> ₽</div>
                            </div>
                        </div>
                    <?}?>
                <?}?>
            </div>
            <?if($arResult['COMPANY']['PROPERTIES']['FILES']['DISPLAY_VALUE'] || $arResult['COMPANY']['PROPERTIES']['FILES_1']['DISPLAY_VALUE']){?>
                <div class="check-company__scan-title">Отправленные сканы документов</div>
                <div class="check-company__scan-list">
                    <?foreach($arResult['COMPANY']['PROPERTIES']['FILES']['DISPLAY_VALUE'] as $file){?>
                        <div class="check-company__document">
                            <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-icon.svg" alt=""><?=$file?>
                        </div>
                    <?}?>
                    <?foreach($arResult['COMPANY']['PROPERTIES']['FILES_1']['DISPLAY_VALUE'] as $file){?>
                        <div class="check-company__document">
                            <img src="<?=SITE_TEMPLATE_PATH?>/img/style/upload-icon.svg" alt=""><?=$file?>
                        </div>
                    <?}?>
                </div>
            <?}?>
        </div>
    </div>
</section>
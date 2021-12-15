<?php
/** @var AwardsSectionController $this */
?>
<!--  Nomination head  -->
<div class="t-page-nominations__header">
    <div class="t-page-nomination__breadcrumbs">
        <div class="t-breadcrumbs">
            <style>
                .t-breadcrumbs__link::after {
                    content: none;
                }
            </style>
            <a href="<?= $this->createUrl('page/awards') ?>" class="t-breadcrumbs__link">Tagline Awards</a> →
            <a href="<?= $this->createUrl('page/awardsWinners20202021') ?>" class="t-breadcrumbs__link">Победители Tagline Awards 2020–2021</a> →
            <?php
            $nom_breadcrumbs = $this->nomination_short;
            if ($this->nomination_1st_short !== $nom_breadcrumbs) {
                $nom_breadcrumbs = sprintf(
                    '<a href="%s" class="t-breadcrumbs__link">%s</a> → %s',
                    $this->nomination_1st_url, $this->nomination_1st_short, $nom_breadcrumbs
                );
            }
            ?>
            <span class="t-breadcrumbs__text-active"><?= $nom_breadcrumbs ?></span>
        </div>
    </div>
    <h1 class="t-page-nominations__title">Победители Tagline Awards 2020–2021 в номинации</h1>
    <div class="t-page-nominations__nav">
        <?php
        if ($this->nom_prev_url) {
            ?>
            <a data-html="true" data-tooltip="Перейти к предыдущей номинации:<br><?= $this->nom_prev_short ?>" href="<?= $this->nom_prev_url ?>" class="t-page-nominations__nav-arrow t-page-nominations__nav-arrow-left">
                <svg width="47" height="47" viewBox="0 0 47 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.29289 24.7071C7.90237 24.3166 7.90237 23.6834 8.29289 23.2929L14.6569 16.9289C15.0474 16.5384 15.6805 16.5384 16.0711 16.9289C16.4616 17.3195 16.4616 17.9526 16.0711 18.3431L10.4142 24L16.0711 29.6569C16.4616 30.0474 16.4616 30.6805 16.0711 31.0711C15.6805 31.4616 15.0474 31.4616 14.6569 31.0711L8.29289 24.7071ZM9 23L39 23L39 25L9 25L9 23Z" fill="black"/>
                </svg>
            </a>
            <?php
        } ?>
            <h2 class="t-page-nominations__title"><?= $this->nom_title_short ?></h2>
        <?php
        if ($this->nom_next_url) {
            ?>
            <a data-html="true" data-tooltip="Перейти к следующей номинации:<br><?= $this->nom_next_short ?>" href="<?= $this->nom_next_url ?>" class="t-page-nominations__nav-arrow t-page-nominations__nav-arrow-right">
                <svg width="47" height="47" viewBox="0 0 47 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M38.7071 24.7071C39.0976 24.3166 39.0976 23.6834 38.7071 23.2929L32.3431 16.9289C31.9526 16.5384 31.3195 16.5384 30.9289 16.9289C30.5384 17.3195 30.5384 17.9526 30.9289 18.3431L36.5858 24L30.9289 29.6569C30.5384 30.0474 30.5384 30.6805 30.9289 31.0711C31.3195 31.4616 31.9526 31.4616 32.3431 31.0711L38.7071 24.7071ZM38 23L8 23L8 25L38 25L38 23Z" fill="black"/>
                </svg>
            </a>
            <?php
        } ?>
    </div>
</div>
<!--  /Nomination head  -->
<?= $this->renderPartial('view/works/container') ?>
<?= $this->renderPartial('nominations-footer') ?>
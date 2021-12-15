<?php
/** @var string $metal */
/** @var array $mr */
/** @var array $w */
?>

<div class="t-page-nominations__awards-items">
    <div class="t-page-nominations__awards-item t-reward-item js-height">
        <div class="t-reward-item__info">
            <div class="t-reward-item__medal">
                <div class="t-reward-item__medal-img">
                    <img src="<?= $this->fileRoot ?>/assets/img/medal-<?= $metal ?>.png" alt="medal-<?= $metal ?>">
                </div>
                <h3 class="t-reward-item__title t-reward-item__title-<?= $metal ?>"><?= $mr[$metal] ?></h3>
            </div>
            <?= $this->renderPartial('view/works/data', ['w' => $w]) ?>
        </div>
        <?php if (isset($w['uploaded_cover'])): ?>
        <div class="t-reward-item__img t-img t-img-large">
            <a href="<?= $w['work_url'] ?>">
                <img class="t-img t-img-large t-img__img t-img__img-<?= $metal ?>" src="<?= $this->getWorkCoverImgRoot() . '/' . $w['uploaded_cover'] ?>"  alt="<?= $w['work_name'] ?>"/>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
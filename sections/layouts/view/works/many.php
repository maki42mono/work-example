<?php
/** @var string $metal */
/** @var array $mr */
/** @var array $w */
/** @var int $order */

$open = $close = false;

if ($order == $this->metals_count[$metal]) {
    $close = true;
}

if ($order % 2 == 1) {
    $open = true;
} else {
    $close = true;
}
?>

<?php if ($open): ?>
<div class="t-page-nominations__awards-items t-page-nominations__awards-items-much">
<?php endif; ?>

    <div class="t-page-nominations__awards-item t-reward-item">
        <div class="t-reward-item__info t-reward-item__info-small">
            <div class="t-reward-item__medal">
                <div class="t-reward-item__medal-img">
                    <img src="<?= $this->fileRoot ?>/assets/img/medal-<?= $metal ?>.png" alt="medal-<?= $metal ?>">
                </div>
                <h3 class="t-reward-item__title t-reward-item__title-<?= $metal ?>"><?= $mr[$metal] ?></h3>
            </div>
            <?= $this->renderPartial('view/works/data', ['w' => $w]) ?>
        </div>
        <?php if (isset($w['uploaded_cover'])): ?>
        <div class="t-reward-item__img t-img t-img-clip">
            <a href="<?= $w['work_url'] ?>">
                <img class="t-img t-img__img t-img__img-<?= $metal ?>" src="<?= $this->getWorkCoverImgRoot() . '/' . $w['uploaded_cover'] ?>" alt="<?= $w['work_name'] ?>"/>
            </a>
        </div>
        <?php endif; ?>
    </div>
<?php if ($close): ?>
</div>
<?php endif; ?>

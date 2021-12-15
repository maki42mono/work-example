<?php
/** @var array $w */
?>
<div class="t-reward-item__description">
    <div class="t-reward-item__description-wrapper">
        <a href="<?= $w['work_url'] ?>" class="t-reward-item__value t-link"><b><?= $w['work_name'] ?></b></a>
    </div>
    <div class="t-reward-item__description-wrapper">
        <span class="t-reward-item__name t-text">Сайт:</span>
        <a href="<?= $w['project_full_url'] ?>" target="_blank" class="t-reward-item__value t-link"><?= $w['project_pretty_url'] ?></a>
    </div>

    <?php if ($w['client'] == $w['performer_name']): ?>
        <div class="t-reward-item__description-wrapper">
            <span class="t-reward-item__name t-text">Заказчик / исполнитель:</span>
            <span class="t-reward-item__value t-text"><?= $w['client'] ?></span>
        </div>
    <?php else: ?>
        <div class="t-reward-item__description-wrapper">
            <span class="t-reward-item__name t-text">Заказчик:</span>
            <span class="t-reward-item__value t-text"><?= $w['client'] ?></span>
        </div>
        <div class="t-reward-item__description-wrapper">
            <span class="t-reward-item__name t-text">Исполнитель:</span>
            <span class="t-reward-item__value t-text"><?= $w['performer_name'] ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($w['coauthor'])): ?>
        <div class="t-reward-item__description-wrapper">
            <span class="t-reward-item__name t-text">Соавторы:</span>
            <span class="t-reward-item__value t-text"><?= $w['coauthor'] ?></span>
        </div>
    <?php endif; ?>
</div>
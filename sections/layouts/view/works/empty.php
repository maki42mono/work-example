<?php
/**
 * @var string $metal
 * @throws Exception
 * @var array $mr
 */

if (!function_exists('awardsMetalInterpreter')) {
    function awardsMetalInterpreter(string $metal): string
    {
        switch ($metal) {
            case 'gold':
            case 'silver':
                return 'присуждалось';
            case 'bronze':
                return 'присуждалась';
            default:
                throw new Exception('Неизвестный металл');
        }
    }
}

?>
<div class="t-page-nominations__awards-items t-page-nominations__awards-items-empty">
    <div class="t-page-nominations__awards-item t-reward-item">
        <div class="t-reward-item__info t-reward-item__info-default">
            <div class="t-reward-item__medal">
                <div class="t-reward-item__medal-img">
                    <!--    TODO: сделать нормально кастомизация лось/лась -->
                    <img src="<?= $this->fileRoot ?>/assets/img/medal-<?= $metal ?>.png" alt="medal-<?= $metal ?>">
                </div>
                <h3 class="t-reward-item__title t-reward-item__title-<?= $metal ?>"><?= $mr[$metal] ?> в данной номинации не <?= awardsMetalInterpreter($metal) ?></h3>
            </div>
        </div>
    </div>
</div>
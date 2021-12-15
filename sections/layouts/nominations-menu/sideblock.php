<aside class="t-page-nominations__sidebar">
    <ul class="t-page-nominations__sidebar-list js-sidebar-list">
        <li class="t-page-nominations__sidebar-item _open">
            <?php $this->renderPartial('nominations-menu/subnominations', [
                'first'          => $this->nomination_1st_short,
                'subnominations' => $this->subnominations,
            ]); ?>
        </li>

        <?php
        foreach ($this->nominations_1st as $key => $value) {
            ?>
            <li class="t-page-nominations__sidebar-item">
                <?php $this->renderPartial('nominations-menu/subnominations', [
                    'first'          => $value['short_name'],
                    'subnominations' => $this->nominations_2nd[$value['translit']],
                ]); ?>
            </li>
            <?php
        } ?>
    </ul>
</aside>
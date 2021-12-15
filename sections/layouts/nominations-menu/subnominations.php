<?= /** @var string $first */
/** @var array $subnominations */
$first ?>
<ul class="t-page-nominations__sidebar-dropdown">
    <?php
    foreach ($subnominations as $key => $value) {
        $style = '';
        $href = [
            '<a href="' . $this->createUrl('view', [
                'year'       => $this->year,
                'nomination' => $value['translit'],
            ]) . '"',
            '</a>',
        ];
        if ($value['translit'] == $this->nomination) {
            $style = 't-page-nominations__sidebar-dropdown-item-active';
            $href = ['<span', '</span>'];
        }
        ?>
        <li class="t-page-nominations__sidebar-dropdown-item <?= $style ?>">
            <?= $href[0]?> class="t-page-nominations__sidebar-link"><?= $value['short_name'] ?><?= $href[1] ?>
        </li>
        <?php
    } ?>
</ul>
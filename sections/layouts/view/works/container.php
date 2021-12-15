<?php
// русские названия металлов
$mr = [
    'gold' => 'Золото',
    'silver' => 'Серебро',
    'bronze' => 'Бронза',
];

$same_metal_order = [];
$nominees = $this->nominees;
$metals_count = call_user_func(function () use ($nominees) {
    $tmp = [];
    foreach ($nominees as $nominee) {
        $tmp[$nominee['metal']]++;
    }
    return $tmp;
});
?>

<div class="t-page-nominations__content">
    <div class="t-page-nominations__awards">
        <?php
        if (count($this->nominees) == 0) {
            echo "Победителей нет!";
        } else {

            for (
                $i = 0,
                $nominees = $this->nominees,
                $metals_list = array_keys($this->metals_count),
                $cur_metal = $metals_list[0],
                $cur_metal_vk = array_flip($metals_list);
                $i < count($nominees); $i++
            ) {
                $w = $nominees[$i];
                $metal = $w['metal'];
                if (($metal != $cur_metal)) {
                    if ($this->metals_count[$cur_metal] == 0) {
                        $this->renderPartial('view/works/empty', [
                            'metal' => $cur_metal,
                            'mr' => $mr,
                        ]);
                    }
                    $next_metal_key = $cur_metal_vk[$cur_metal] + 1;
                    if (isset($metals_list[$next_metal_key])) {
                        $cur_metal = $metals_list[$next_metal_key];
                    }
                    $i--;
                } elseif ($this->metals_count[$metal] == 1 || /** Если это последняя карточка с нечетным кол-вом —
                     * выводим ее как одиночную */
                    ($metals_count[$metal] % 2 == 1 && $same_metal_order[$metal] == $metals_count[$metal] - 1)) {
                    $this->renderPartial('view/works/only', compact('metal', 'mr', 'w'));
                } elseif ($this->metals_count[$metal] > 1) {
                    $order = ++$same_metal_order[$metal];
                    $this->renderPartial('view/works/many', compact('metal', 'mr', 'w', 'order'));
                }
            }
        } ?>
    </div>
    <?php $this->renderPartial('nominations-menu/sideblock'); ?>
</div>
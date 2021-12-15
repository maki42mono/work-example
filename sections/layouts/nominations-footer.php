<div class="t-page-nominations__footer">
    <div class="t-page-nominations__footer-col t-page-nominations__footer-col-left">
        <?php
        if ($this->nom_prev_url) {
            ?>
                <span class="t-page-nominations__footer-nav">
                    <svg width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.646446 3.64645C0.451184 3.84171 0.451184 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.97631 4.7308 0.659727 4.53553 0.464465C4.34027 0.269203 4.02369 0.269203 3.82843 0.464465L0.646446 3.64645ZM16 3.5L1 3.5L1 4.5L16 4.5L16 3.5Z" fill="#3472C9"/>
                    </svg>
                    <a href="<?= $this->nom_prev_url ?>" class="t-link">Перейти к предыдущей номинации:<br><?= $this->nom_prev_short ?></a>
                </span>
        <span class="t-text"></span>
        <?php
        } ?>
    </div>

    <div class="t-page-nominations__footer-col t-page-nominations__footer-col-center">
        <a href="<?= $this->nom_rand_url ?>" class="t-link">Перейти к случайной номинации</a>
    </div>
    <div class="t-page-nominations__footer-col t-page-nominations__footer-col-right">
        <?php
        if ($this->nom_next_url) {
            ?>
            <span class="t-page-nominations__footer-nav">
                        <a href="<?= $this->nom_next_url ?>" class="t-link">Перейти к следующей номинации:<br><?= $this->nom_next_short ?></a>
                        <svg width="16" height="8" viewBox="0 0 16 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.3536 3.64645C15.5488 3.84171 15.5488 4.15829 15.3536 4.35355L12.1716 7.53553C11.9763 7.7308 11.6597 7.7308 11.4645 7.53553C11.2692 7.34027 11.2692 7.02369 11.4645 6.82843L14.2929 4L11.4645 1.17157C11.2692 0.97631 11.2692 0.659727 11.4645 0.464465C11.6597 0.269203 11.9763 0.269203 12.1716 0.464465L15.3536 3.64645ZM-4.37112e-08 3.5L15 3.5L15 4.5L4.37112e-08 4.5L-4.37112e-08 3.5Z" fill="#3472C9"/>
                        </svg>
                    </span>
            <?php /*
            <span class="t-text"><?= $this->nom_next_short ?></span>
 */?>
            <?php
        } ?>

    </div>
</div>
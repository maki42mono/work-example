<?php

interface OldWinnersInterface
{
    public static function getAwardsId(): int;

    public static function getAwardsWinnersAnySort(): array;

    public static function getAgencyMedalStatusCleared(array $winners_data): array;

    public static function getSpecialWinners(): array;
}
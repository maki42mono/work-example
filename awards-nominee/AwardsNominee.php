<?php

interface AwardsNominee
{
    public function getTranslit();

    public function getMasterId(): int;

    public function getTableName(): string;

    public function getNomineeName(): string;

    public function getProceeds(): float;

    public function getContacts(): string;

    public static function getCurAwardsNomineesWithEditStats(): array;

    public static function getAllAwardsInfo4Call(): array;

}
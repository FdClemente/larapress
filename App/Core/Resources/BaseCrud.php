<?php

namespace App\Core\Resources;

interface BaseCrud
{
    public function generateActions();
    public function getPages();
    public function getActions();
    public static function getRoute();
}
<?php

namespace App\Policies;

class ProductPolicy extends ModulePolicy
{
    protected string $module = 'products';

    protected bool $seoEditable = true;
}

<?php

namespace App\Policies;

class PortfolioPolicy extends ModulePolicy
{
    protected string $module = 'portfolio';

    protected bool $seoEditable = true;
}

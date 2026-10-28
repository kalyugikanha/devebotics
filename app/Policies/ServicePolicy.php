<?php

namespace App\Policies;

class ServicePolicy extends ModulePolicy
{
    protected string $module = 'services';

    protected bool $seoEditable = true;
}

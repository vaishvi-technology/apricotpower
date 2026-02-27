<?php

namespace App\Lunar;

use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductTypeResourceExtension extends ResourceExtension
{
    /**
     * Hide ProductType from navigation sidebar.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ProductCategory\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;

class ProductNotFoundException extends Exception
{
    public function __construct(UuidV7 $productId)
    {
        parent::__construct(sprintf('Product %s not found.', $productId));
    }
}
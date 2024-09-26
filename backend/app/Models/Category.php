<?php

namespace App\Models;

use Illuminate\Support\Enumerable;
use JsonSerializable;


class Category implements JsonSerializable
{
    private array $subCategories = [];
    private string $name;
    private ?string $link;
    private array $products = [];

    public function __construct(
        string $name,
        ?string $link = null
    ) {
        $this->name = $name;
        $this->link = $link;
    }

    public function addSubCategory(Category $subCategory) : void
    {
        $this->subCategories[] = $subCategory;
    }

    public function addProduct(Product $product) : void
    {
        $this->products[] = $product;
    }

    public function getSubCategories() : array
    {
        return $this->subCategories;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getLink() : ?string
    {
        return $this->link;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'link' => $this->link,
            'products' => $this->products,
            'subCategories' => $this->subCategories,
        ];
    }
}

<?php

namespace App\Models;
use JsonSerializable;

class Product implements JsonSerializable
{
    private string $productName;
    private string $productCode;
    private string $price;
    private ?string $oldPrice;
    private string $availabilityStatus;

    // Constructor
    public function __construct(string $productName, string $productCode, string $price, ?string $oldPrice, string $availabilityStatus)
    {
        $this->productName = $productName;
        $this->productCode = $productCode;
        $this->price = $price;
        $this->oldPrice = $oldPrice;
        $this->availabilityStatus = $availabilityStatus;
    }

    // Getter for productName
    public function getProductName(): string
    {
        return $this->productName;
    }

    // Getter for productCode
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    // Getter for price
    public function getPrice(): string
    {
        return $this->price;
    }

    // Getter for discountedPrice
    public function getOldPrice(): ?string
    {
        return $this->oldPrice;
    }

    // Getter for availabilityStatus
    public function getAvailabilityStatus(): string
    {
        return $this->availabilityStatus;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'productName' => $this->productName,
            'productCode' => $this->productCode,
            'price' => $this->price,
            'oldPrice' => $this->oldPrice,
            'availabilityStatus' => $this->availabilityStatus,
        ];
    }
}
?>


<?php

namespace App\Contracts;

interface IDtoContract
{
    /**
     * Converts an array of data to a compliant/typed DTO
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self;

    /**
     * Converts DTO's to array
     *
     * @return array
     */
    public function toArray(): array;
}
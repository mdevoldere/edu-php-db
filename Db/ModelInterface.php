<?php 

namespace Md\Db;


interface ModelInterface
{
    /**
     * Get current Model identifier
     * @return int|string the current model identifier
     */
    public function getId(): mixed;

    /**
     * Check if model data is valid
     * @return bool true if model is valid, false otherwrise
     */
    public function isValid(): bool;

    /**
     * Get the current Model data as array
     * @return array the current model data
     */
    public function toArray(): array;
}

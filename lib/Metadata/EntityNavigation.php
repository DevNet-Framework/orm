<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\Entity\Annotations\ForeignKey;
use DevNet\System\Generic;
use DevNet\System\Tweak;
use ReflectionProperty;

class EntityNavigation
{
    use Tweak;

    public const One  = 1;
    public const Many = 2;

    private EntityType $entityType;
    private EntityType $targetEntityType;
    private ReflectionProperty $propertyInfo;
    private int $cardinality;

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo, int $cardinality = 0)
    {
        $this->entityType   = $entityType;
        $this->propertyInfo = $propertyInfo;
        $this->cardinality  = $cardinality;

        switch ($cardinality) {
            case 2:
                foreach ($this->propertyInfo->getAttributes() as $attribute) {
                    if ($attribute->getName() == Generic::class) {
                        $collection = $attribute->newInstance();
                        $types = $collection->getTypes();
                        if ($types[0]->isClass()) {
                            $this->hasMany($types[0]);
                            return;
                        }
                    }
                }
                $this->cardinality = 0;
                break;
            case 1:
                foreach ($this->propertyInfo->getAttributes() as $attribute) {
                    if ($attribute->getName() == ForeignKey::class) {
                        $foreignKey = $attribute->newInstance();
                        $this->hasForeignKey($foreignKey->getPropertyName());
                        return;
                    }
                }
                break;
        }
    }

    public function get_PropertyInfo(): ReflectionProperty
    {
        return $this->propertyInfo;
    }

    public function get_EntityType(): EntityType
    {
        return $this->entityType;
    }

    public function get_TargetEntityType(): EntityType
    {
        return $this->targetEntityType;
    }

    public function get_Cardinality(): int
    {
        return $this->cardinality;
    }

    public function hasMany(string $relatedEntity): void
    {
        $this->targetEntityType = $this->metadata->Model->getEntityType($relatedEntity);
        $this->cardinality = 2;
    }

    public function hasForeignKey(string $propertyName): void
    {
        $navigationType = $this->propertyInfo->getType()->getName();
        $this->entityType->addForeignKey($propertyName,  $navigationType);
    }
}

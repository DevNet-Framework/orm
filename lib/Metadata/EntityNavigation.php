<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Metadata;

use DevNet\ORM\Annotations\ForeignKey;
use DevNet\System\Collections\ICollection;
use DevNet\System\PropertyTrait;
use DevNet\System\Type;
use ReflectionProperty;

class EntityNavigation
{
    use PropertyTrait;

    public const One  = 1;
    public const Many = 2;

    private EntityType $entityType;
    private string $targetEntity;
    private ReflectionProperty $propertyInfo;
    private int $cardinality = 0;

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo, int $cardinality = 0)
    {
        $this->entityType   = $entityType;
        $this->propertyInfo = $propertyInfo;

        switch ($cardinality) {
            case 2:
                $attribute = $this->propertyInfo->getAttributes(Type::class)[0] ?? null;
                if ($attribute) {
                    $type = $attribute->newInstance();
                    if ($type->Name == ICollection::class) {
                        $types = $type->getGenericArguments();
                        if ($types && $types[0]->isClass()) {
                            $this->hasMany($types[0]);
                        }
                    }
                }
                break;
            case 1:
                $attribute = $this->propertyInfo->getAttributes(ForeignKey::class)[0] ?? null;
                if ($attribute) {
                    $foreignKey = $attribute->newInstance();
                    $this->hasForeignKey($foreignKey->getPropertyName());
                } else {
                    $this->targetEntity = $this->propertyInfo->getType()->getName();
                    $this->cardinality = 1;
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

    public function get_TargetEntity(): ?string
    {
        return $this->targetEntity ?? null;
    }

    public function get_Cardinality(): int
    {
        return $this->cardinality;
    }

    public function hasMany(string $relatedEntity): void
    {
        $this->targetEntity = $relatedEntity;
        $this->cardinality = 2;
    }

    public function hasForeignKey(string $propertyName): void
    {
        $this->targetEntity = $this->propertyInfo->getType()->getName();
        $this->entityType->addForeignKey($propertyName, $this->targetEntity);
        $this->cardinality = 1;
    }
}

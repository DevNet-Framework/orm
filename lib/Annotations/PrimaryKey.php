<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Annotations;

use Attribute;

#[Attribute]
class PrimaryKey
{
   private array $keys = [];

   public function __construct(string $propertyName, string ...$propertyNames)
   {
      $this->keys[] = $propertyName;

      foreach ($propertyNames as $propertyName) {
         $this->keys[] = $propertyName;
      }
   }

   public function getKeys(): array
   {
      return $this->keys;
   }
}

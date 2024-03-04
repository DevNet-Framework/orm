<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
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

<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Annotations;

use Attribute;

#[Attribute]
class ForeignKey
{
   private string $propertyName;

   public function __construct(string $propertyName)
   {
      $this->propertyName = $propertyName;
   }

   public function getPropertyName(): string
   {
      return $this->propertyName;
   }
}

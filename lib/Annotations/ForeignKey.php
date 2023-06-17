<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Annotations;

use DevNet\System\PropertyTrait;
use Attribute;

#[Attribute]
class ForeignKey
{
   use PropertyTrait;

   private string $propertyName;

   public function __construct(string $propertyName)
   {
      $this->propertyName = $propertyName;
   }

   public function get_PropertyName(): string
   {
      return $this->propertyName;
   }
}

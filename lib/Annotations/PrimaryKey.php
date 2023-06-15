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
class PrimaryKey
{
   use PropertyTrait;

   private array $keys;

   public function __construct(string ...$propertyName)
   {
      $this->keys = $propertyName;
   }

   public function get_Keys(): array
   {
      return $this->keys;
   }
}

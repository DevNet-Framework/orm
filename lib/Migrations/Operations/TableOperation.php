<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

abstract class TableOperation extends Operation
{
    public string $Name;
    public ?string $Schema = null;
    public array $Columns = [];
    public array $Constraints = [];

    public function __construct(string $name, ?string $schema = null)
    {
        $this->Name = $name;
        $this->Schema = $schema;
    }
}

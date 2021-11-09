<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

abstract class OperationVisitor
{
    protected array $statment;

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function visit(Operation $operation): void
    {
        $operation->accept($this);
    }

    abstract public function visitCreateTable(Operation $operation): void;

    abstract public function visitAlterTable(Operation $operation): void;
    
    abstract public function visitRenameTable(Operation $operation): void;

    abstract public function visitDropTable(Operation $operation): void;

    abstract public function visitColumn(Operation $operation): void;

    abstract public function visitAddColumn(Operation $operation): void;

    abstract public function visitAlterColumn(Operation $operation): void;
    
    abstract public function visitRenameColumn(Operation $operation): void;

    abstract public function visitDropColumn(Operation $operation): void;

    abstract public function visitPrimaryKey(Operation $operation): void;

    abstract public function visitAddPrimaryKey(Operation $operation): void;

    abstract public function visitDropPrimaryKey(Operation $operation): void;

    abstract public function visitForeignKey(Operation $operation): void;

    abstract public function visitAddForeignKey(Operation $operation): void;

    abstract public function visitDropForeignKey(Operation $operation): void;

    abstract public function visitUniqueConstraint(Operation $operation): void;

    abstract public function visitAddUniqueConstraint(Operation $operation): void;

    abstract public function visitDropUniqueConstraint(Operation $operation): void;

    abstract public function visitInsertData(Operation $operation): void;

    abstract public function visitUpdateData(Operation $operation): void;

    abstract public function visitDeleteData(Operation $operation): void;
}

<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Sqlite;

use DevNet\ORM\Migrations\Operations\OperationVisitor;
use DevNet\ORM\Migrations\Operations\Operation;
use DevNet\System\Text\StringBuilder;

class SqliteMigrationGenerator extends OperationVisitor
{
    protected SqliteHelper $sqlHelper;

    public function __construct()
    {
        $this->SqlBuilder = new StringBuilder();
        $this->sqlHelper  = new SqliteHelper();
    }

    public function visitTable(Operation $operation): void
    {
        $index = 0;
        $count = count($operation->Columns);
        foreach ($operation->Columns as $column) {
            $this->visit($column);
            if ($index < $count - 1 || $operation->Constraints) {
                $this->SqlBuilder->appendLine(',');
            }
            $index++;
        }

        $index = 0;
        $count = count($operation->Constraints);
        foreach ($operation->Constraints as $constraint) {
            $this->visit($constraint);
            if ($index < $count - 1) {
                $this->SqlBuilder->appendLine(',');
            }
            $index++;
        }
    }

    public function visitCreateTable(Operation $operation): void
    {
        $table = $this->sqlHelper->delimitIdentifier($operation->Name, $operation->Schema);
        $this->SqlBuilder->append('CREATE TABLE ');
        $this->SqlBuilder->append($table);
        $this->SqlBuilder->appendLine(' (');
        $this->visitTable($operation);
        $this->SqlBuilder->appendLine(');');
    }

    public function visitAlterTable(Operation $operation): void
    {
        $table = $this->sqlHelper->delimitIdentifier($operation->Name, $operation->Schema);
        foreach ($operation->Columns as $column) {
            $this->SqlBuilder->append('ALTER TABLE ');
            $this->SqlBuilder->appendLine($table);
            $this->visit($column);
            $this->SqlBuilder->appendLine(',');
        }

        foreach ($operation->Constraints as $constraint) {
            $this->SqlBuilder->append('ALTER TABLE ');
            $this->SqlBuilder->appendLine($table);
            $this->visit($constraint);
            $this->SqlBuilder->appendLine(',');
        }
    }

    public function visitRenameTable(Operation $operation): void
    {
        $table = $this->sqlHelper->delimitIdentifier($operation->Name, $operation->Schema);
        $rename = $this->sqlHelper->delimitIdentifier($operation->Rename, $operation->Schema);
        $this->SqlBuilder->append('RENAME TABLE ');
        $this->SqlBuilder->append($table);
        $this->SqlBuilder->append(' TO ');
        $this->SqlBuilder->append($rename);
        $this->SqlBuilder->appendLine(';');
    }

    public function visitDropTable(Operation $operation): void
    {
        $table = $this->sqlHelper->delimitIdentifier($operation->Name, $operation->Schema);
        $this->SqlBuilder->append('DROP TABLE ');
        $this->SqlBuilder->append($table);
        $this->SqlBuilder->appendLine(';');
    }

    public function visitColumn(Operation $operation): void
    {
        $column = $this->sqlHelper->delimitIdentifier($operation->Name);
        $this->SqlBuilder->append($column);
        switch ($operation->Type) {
            case 'boolean':
            case 'bool':
            case 'int':
            case 'smallint':
            case 'bigint':
                $this->SqlBuilder->append(' INTEGER');
                break;
            case 'decimal':
                $this->SqlBuilder->append(' NUMERIC');
                break;
            case 'float':
            case 'double':
                $this->SqlBuilder->append(' REAL');
                break;
            case 'string':
                $this->SqlBuilder->append(' TEXT');
                break;
            default:
            $this->SqlBuilder->append(' ');
            $this->SqlBuilder->append(strtoupper($operation->Type));
                break;
        }

        if ($operation->Nullable) {
            $this->SqlBuilder->append(' NULL');
        } else {
            $this->SqlBuilder->append(' NOT NULL');
        }

        if ($operation->Default) {
            $this->SqlBuilder->append(' DEFAULT ');
            if (is_numeric($operation->Default) || is_bool($operation->Default)) {
                $this->SqlBuilder->append($operation->Default);
            } else {
                $this->SqlBuilder->append("'{$operation->Default}'");
            }
        }

        if ($operation->Identity) {
            $this->SqlBuilder->append(' AUTO_INCREMENT');
        }
    }

    public function visitAddColumn(Operation $operation): void
    {
        $this->SqlBuilder->append('ADD COLUMN ');
        $this->visitColumn($operation);
    }

    public function visitAlterColumn(Operation $operation): void
    {
        throw new \Exception("ALTER COLUMN not supported in Sqlite Database");
    }

    public function visitRenameColumn(Operation $operation): void
    {
        $name   = $this->sqlHelper->delimitIdentifier($operation->Name);
        $rename = $this->sqlHelper->delimitIdentifier($operation->Rename);
        $this->SqlBuilder->append('RENAME COLUMN ');
        $this->SqlBuilder->append($name);
        $this->SqlBuilder->append(' TO ');
        $this->SqlBuilder->append($rename);
    }

    public function visitDropColumn(Operation $operation): void
    {
        $name = $this->sqlHelper->delimitIdentifier($operation->Name);
        $this->SqlBuilder->append('DROP COLUMN ');
        $this->SqlBuilder->append($name);
    }

    public function visitPrimaryKey(Operation $operation): void
    {
        $keys = implode(', ', $operation->Columns);
        $keys = $this->sqlHelper->delimitIdentifier($keys);
        $constraint = $this->sqlHelper->delimitIdentifier($operation->Constraint);
        $this->SqlBuilder->append('CONSTRAINT ');
        $this->SqlBuilder->append($constraint);
        $this->SqlBuilder->append(' PRIMARY KEY (');
        $this->SqlBuilder->append($keys);
        $this->SqlBuilder->append(')');
    }

    public function visitAddPrimaryKey(Operation $operation): void
    {
        $this->SqlBuilder->append('ADD ');
        $this->visitPrimaryKey($operation);
    }

    public function visitDropPrimaryKey(Operation $operation): void
    {
        $this->SqlBuilder->append('DROP PRIMARY KEY');
    }

    public function visitForeignKey(Operation $operation): void
    {
        $key = $this->sqlHelper->delimitIdentifier($operation->Column);
        $constraint = $this->sqlHelper->delimitIdentifier($operation->Constraint);
        $refTable = $this->sqlHelper->delimitIdentifier($operation->ReferencedTable);
        $refColumn = $this->sqlHelper->delimitIdentifier($operation->ReferencedColumn);
        $this->SqlBuilder->append('CONSTRAINT ');
        $this->SqlBuilder->append($constraint);
        $this->SqlBuilder->append(' FOREIGN KEY (');
        $this->SqlBuilder->append($key);
        $this->SqlBuilder->append(')');
        $this->SqlBuilder->appendLine();
        $this->SqlBuilder->append('REFERENCES ');
        $this->SqlBuilder->append($refTable);
        $this->SqlBuilder->append(' (');
        $this->SqlBuilder->append($refColumn);
        $this->SqlBuilder->append(')');

        if ($operation->OnUpdate) {
            $this->SqlBuilder->appendLine();
            $this->SqlBuilder->append('ON UPDATE ');
            $this->SqlBuilder->append($operation->OnUpdate);
        }

        if ($operation->OnDelete) {
            $this->SqlBuilder->appendLine();
            $this->SqlBuilder->append('ON DELETE ');
            $this->SqlBuilder->append($operation->OnDelete);
        }
    }

    public function visitAddForeignKey(Operation $operation): void
    {
        $this->SqlBuilder->append('ADD ');
        $this->visitForeignKey($operation);
    }

    public function visitDropForeignKey(Operation $operation): void
    {
        $constraint = $this->sqlHelper->delimitIdentifier($operation->Constraint);
        $this->SqlBuilder->append('DROP FOREIGN KEY ');
        $this->SqlBuilder->append($constraint);
    }

    public function visitUniqueConstraint(Operation $operation): void
    {
        $columns = implode(', ', $operation->Columns);
        $columns = $this->sqlHelper->delimitIdentifier($columns);
        $constraint = $this->sqlHelper->delimitIdentifier($operation->Constraint);
        $this->SqlBuilder->append('CONSTRAINT ');
        $this->SqlBuilder->append($constraint);
        $this->SqlBuilder->append(' UNIQUE (');
        $this->SqlBuilder->append($columns);
        $this->SqlBuilder->append(')');
    }

    public function visitAddUniqueConstraint(Operation $operation): void
    {
        $this->SqlBuilder->append('ADD ');
        $this->visitUniqueConstraint($operation);
    }

    public function visitDropUniqueConstraint(Operation $operation): void
    {
        $constraint = $this->sqlHelper->delimitIdentifier($operation->Constraint);
        $this->SqlBuilder->append('DROP INDEX ');
        $this->SqlBuilder->append($constraint);
    }

    public function visitInsertData(Operation $operation): void
    {

        $table = $this->sqlHelper->delimitIdentifier($operation->Table, $operation->Schema);
        $columnNames  = [];
        $columnValues = [];
        foreach ($operation->Columns as $name => $value) {
            $columnNames[] = $this->sqlHelper->delimitIdentifier($name);
            if (is_numeric($value) || is_bool($value)) {
                $columnValues[] = $value;
            } else {
                $columnValues[] = "'{$value}'";
            }
        }

        $columnNames  = implode(', ', $columnNames);
        $columnValues = implode(', ', $columnValues);

        $this->SqlBuilder->append('INSERT INTO ');
        $this->SqlBuilder->append($table);
        $this->SqlBuilder->append(' (');
        $this->SqlBuilder->append($columnNames);
        $this->SqlBuilder->append(') ');
        $this->SqlBuilder->append('VALUES (');
        $this->SqlBuilder->append($columnValues);
        $this->SqlBuilder->append(');');
    }

    public function visitUpdateData(Operation $operation): void
    {
        $table   = $this->sqlHelper->delimitIdentifier($operation->Table, $operation->Schema);
        $columns = [];
        $keys    = [];
        foreach ($operation->Columns as $name => $value) {
            if (is_numeric($value) || is_bool($value)) {
                $columns[] = $this->sqlHelper->delimitIdentifier($name) . " = {$value}";
            } else {
                $columns[] = $this->sqlHelper->delimitIdentifier($name) . " = '{$value}'";
            }
        }

        foreach ($operation->Keys as $name => $value) {
            if (is_numeric($value) || is_bool($value)) {
                $keys[] = $this->sqlHelper->delimitIdentifier($name) . " = {$value}";
            } else {
                $keys[] = $this->sqlHelper->delimitIdentifier($name) . " = '{$value}'";
            }
        }

        $columns = implode(', ', $columns);
        $keys    = implode(' AND ', $keys);

        $this->SqlBuilder->append('UPDATE ');
        $this->SqlBuilder->append($table);
        $this->SqlBuilder->append(' SET ');
        $this->SqlBuilder->append($columns);
        $this->SqlBuilder->append(' WHERE ');
        $this->SqlBuilder->append($keys);
        $this->SqlBuilder->append(';');
    }

    public function visitDeleteData(Operation $operation): void
    {
        $table = $this->sqlHelper->delimitIdentifier($operation->Table, $operation->Schema);
        $keys  = [];

        foreach ($operation->Keys as $name => $value) {
            if (is_numeric($value) || is_bool($value)) {
                $keys[] = $this->sqlHelper->delimitIdentifier($name) . " = {$value}";
            } else {
                $keys[] = $this->sqlHelper->delimitIdentifier($name) . " = '{$value}'";
            }
        }

        $keys = implode(' AND ', $keys);

        $this->SqlBuilder->append('DELETE FROM ');
        $this->SqlBuilder->append($table);
        if ($keys) {
            $this->SqlBuilder->append(' WHERE ');
            $this->SqlBuilder->append($keys);
        }
        $this->SqlBuilder->append(';');
    }
}

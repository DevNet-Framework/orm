<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\Sqlite;

use DevNet\Entity\Metadata\EntityType;
use DevNet\System\Compiler\ExpressionStringBuilder;
use DevNet\System\Compiler\Expressions\Expression;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;
use DevNet\System\Exceptions\PropertyException;
use DevNet\System\Linq\IQueryable;

class SqliteQueryGenerator extends ExpressionVisitor
{
    private EntityType $entityType;
    private string $method         = '';
    private string $lastMethod     = '';
    private array $parameters     = [];
    public array $OuterVariables  = [];
    public array $Sql             = [];

    public static function expressionToString(Expression $expression): string
    {
        $visitor = new ExpressionStringBuilder();
        $visitor->visit($expression);
        return $visitor->__toString();
    }

    public function visitLambda(Expression $expression): void
    {
        $this->parameters = $expression->Parameters;
        $this->visit($expression->Body);
    }

    public function visitCall(Expression $expression): void
    {
        $arguments = $expression->Arguments;
        $lastExpression = array_shift($arguments);
        if ($lastExpression) {
            $this->visit($lastExpression);
        }

        $this->method = strtolower($expression->Method);
        switch ($this->method) {
            case 'where':
                $this->Sql[] = 'WHERE';
                break;
            case 'str_contains':
            case 'str_starts_with':
            case 'str_ends_with':
                $this->Sql[] = "LIKE";
                break;
            case 'orderby':
            case 'orderbydescending':
                $this->Sql[] = "ORDER BY";
                break;
            case 'thenby':
            case 'thenbydescending':
                $this->Sql[] = ",";
                break;
            case 'skip':
                if ($this->lastMethod === "take") {
                    $this->Sql[] = "OFFSET";
                } else {
                    $this->Sql[] = "LIMIT";
                    $this->Sql[] = 18446744073709551615;
                    $this->Sql[] = "OFFSET";
                }
                break;
            case 'take':
                $this->Sql[] = 'LIMIT';
                break;
            case 'groupby':
                $this->Sql[] = 'GROUP BY';
                break;
            default:
                # code...
                break;
        }

        foreach ($arguments as $argument) {
            $this->visit($argument);
        }

        if ($this->method === "orderby" || $this->method === "thenby") {
            $this->Sql[] = "ASC";
        } else if ($this->method === "orderbydescending" || $this->method === "thenbydescending") {
            $this->Sql[] = "DESC";
        }

        if ($this->method === "take" && $this->lastMethod === "skip") {
            $stack = [];

            for ($i = 0; $i < 3; $i++) {
                $swap    = array_pop($this->Sql);
                $stack[] = array_pop($this->Sql);
                $stack[] = $swap;
            }

            for ($i = 0; $i < 4; $i++) {
                $this->Sql[] = array_shift($stack);
            }
        }

        $this->lastMethod = $expression->Method;
    }

    public function visitGroup(Expression $expression): void
    {
        $this->Sql[] = "(";
        $this->visit($expression->Expression);
        $this->Sql[] = ")";
    }

    public function visitBinary(Expression $expression): void
    {
        $negation = null;
        switch ($expression->Name) {
            case "!=":
                $operator = "=";
                $negation = "NOT";
                break;
            case "==":
                $operator = "=";
                break;
            case "&&":
                $operator = "AND";
                break;
            case "||":
                $operator = "OR";
                break;
            default:
                $operator = $expression->Name;
                break;
        }

        if ($negation) {
            $this->Sql[] = $negation;
        }
        $this->visit($expression->Left);
        $this->Sql[] = $operator;
        $this->visit($expression->Right);
    }

    public function visitUnary(Expression $expression): void
    {
        $operator = $expression->Name;
        if ($expression->Name === "!") {
            $operator = "NOT ";
        }

        $this->Sql[] = $operator;
        $this->visit($expression->Operand);
    }

    public function visitProperty(Expression $expression): void
    {
        if (in_array($expression->Parameter->Name, $this->parameters)) {
            $propertyType = $this->entityType->getProperty($expression->Property);
            if (!$propertyType) {
                throw new PropertyException("undefined property {$this->entityType->EntityName}::{$expression->Property}");
            }
            $this->Sql[] = $propertyType->Column['Name'];
        } else {
            $property = $expression->Property;
            $this->OuterVariables[] = $expression->Parameter->Value->$property;
            $this->Sql[] = "?";
        }
    }

    public function visitParameter(Expression $expression): void
    {
        if (in_array($expression->Name, $this->parameters)) {
            $this->Sql[] = $expression->Name;
        } else {
            $this->OuterVariables[] = $expression->Value;
            $this->Sql[] = "?";
        }
    }

    public function visitConstant(Expression $expression): void
    {
        if ($expression->Value instanceof IQueryable) {
            $this->entityType = $expression->Value->EntityType;
            $this->Sql[] = "SELECT * FROM {$this->entityType->getTableName()}";
            $this->lastMethod = "from";
        } else {
            switch ($this->method) {
                case 'str_contains':
                    $this->Sql[] = "'%" . $expression->Value . "%'";
                    break;
                case 'str_starts_with':
                    $this->Sql[] = "{$expression->Value}%";
                    break;
                case 'str_ends_with':
                    $this->Sql[] = "%{$expression->Value}";
                    break;
                default:
                    if ($expression->Type === "string") {
                        $this->Sql[] = "'{$expression->Value}'";
                        break;
                    } else if ($expression->Type === "bool") {
                        $this->Sql[] = $expression->Value == true ? "true" : "false";
                        break;
                    }
                    $this->Sql[] = $expression->Value;
                    break;
            }
        }
    }

    public function visitArray(Expression $expression): void
    {
        throw new \Exception("Array Expression not implemented!");
    }

    public function __toString(): string
    {
        return implode(" ", $this->Sql);
    }
}

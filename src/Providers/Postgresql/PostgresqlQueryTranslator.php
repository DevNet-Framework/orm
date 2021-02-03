<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Providers\Postgresql;

use Artister\System\Compiler\ExpressionVisitor;
use Artister\System\Compiler\ExpressionStringBuilder;
use Artister\System\Compiler\Expressions\Expression;
use Artister\System\Linq\IQueryable;

class PostgresqlQueryTranslator extends ExpressionVisitor
{
    public string $Method           = '';
    public string $LastMethod       = '';
    private array $Parameters       = [];
    public array $OuterVariables    = [];
    public array $Sql               = [];

    public static function expressionToString(Expression $expression) : string
    {
        $visitor = new ExpressionStringBuilder();
        $visitor->visit($expression);
        return $visitor->__toString();
    }

    public function visitLambda(Expression $expression)
    {
        $this->Parameters = $expression->Parameters;
        $this->visit($expression->Body);
    }

    public function visitCall(Expression $expression)
    {
        $arguments = $expression->Arguments;
        $lastExpression = array_shift($arguments);
        if ($lastExpression)
        {
            $this->visit($lastExpression);
        }

        $this->Method = strtolower($expression->Method);
        switch ($this->Method)
        {
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
                if ($this->LastMethod == "take")
                {
                    $this->Sql[] = "OFFSET";
                }
                else{
                    $this->Sql[] = "LIMIT";
                    $this->Sql[] = "ALL";
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
        
        foreach ($arguments as $argument)
        {
            $this->visit($argument);
        }

        if ($this->Method == "orderby" || $this->Method == "thenby")
        {
            $this->Sql[] = "ASC";
        }
        else if ($this->Method == "orderbydescending" || $this->Method == "thenbydescending")
        {
            $this->Sql[] = "DESC";
        }

        if ($this->Method == "take" && $this->LastMethod == "skip")
        {
            $stack = [];

            for ($i = 0; $i < 3 ; $i++)
            { 
                $swap       = array_pop($this->Sql);
                $stack[]    = array_pop($this->Sql);
                $stack[]    = $swap;
            }

            for ($i = 0; $i < 4; $i++)
            { 
                $this->Sql[] = array_shift($stack);
            }
        }

        $this->LastMethod = $expression->Method;
    }

    public function visitGroup(Expression $expression)
    {
        $this->Sql[] = "(";
        $this->visit($expression->Expression);
        $this->Sql[] = ")";
    }

    public function visitBinary(Expression $expression)
    {   
        $negation = null;
        switch ($expression->Name)
        {
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
        
        if ($negation)
        {
            $this->Sql[] = $negation;
        }
        $this->visit($expression->Left);
        $this->Sql[] = $operator;
        $this->visit($expression->Right);
    }

    public function visitUnary(Expression $expression)
    {
        $operator = $expression->Name;
        if ($expression->Name == "!")
        {
            $operator = "NOT ";
        }

        $this->Sql[] = $operator;
        $this->visit($expression->Operand);
    }

    public function visitProperty(Expression $expression)
    {
        if (in_array($expression->Parameter->Name, $this->Parameters))
        {
            $this->Sql[] = "\"{$expression->Property}\"";
        }
        else
        {
            $this->Sql[] = "?";
        }
    }

    public function visitParameter(Expression $expression)
    {
        if (in_array($expression->Name, $this->Parameters))
        {
            $this->Sql[] = $expression->Name;
        }
        else
        {
            $this->OuterVariables[] = $expression->Value;
            $this->Sql[] = "?";
        }
    }

    public function visitConstant(Expression $expression)
    {
        if ($expression->Value instanceof IQueryable)
        {
            $this->Sql[] = "SELECT * FROM \"{$expression->Value->EntityType->getTableName()}\"";
            $this->LastMethod = "from";
        }
        else
        {
            switch ($this->Method)
            {
                case 'str_contains':
                    $this->Sql[] = "'%".$expression->Value."%'";
                    break;
                case 'str_starts_with':
                    $this->Sql[] = "{$expression->Value}%";
                    break;
                case 'str_ends_with':
                    $this->Sql[] = "%{$expression->Value}";
                    break;
                default:
                    if ($expression->Type == "string")
                    {
                        $this->Sql[] = "'{$expression->Value}'";
                        break;
                    }
                    else if ($expression->Type == "bool")
                    {
                        $this->Sql[] = $expression->Value == true ? "true" : "false";
                        break;
                    }
                    $this->Sql[] = $expression->Value;
                    break;
            }
        }
    }

    public function visitArray(Expression $expression)
    {
        throw new \Exception("Array Expression not implemented!");
    }

    public function __toString()
    {
        return implode(" ", $this->Sql);
    }
}
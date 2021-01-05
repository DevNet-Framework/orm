<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Providers\Mysql;

use Artister\System\Compiler\ExpressionVisitor;
use Artister\System\Compiler\ExpressionStringBuilder;
use Artister\System\Compiler\Expressions\Expression;
use Artister\System\Linq\IQueryable;


class MysqlQueryTranslator extends ExpressionVisitor
{
    public string $Out              = '';
    public string $Method           = '';
    private array $Parameters       = [];
    public array $OuterVariables    = [];

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

        $method = strtolower($expression->Method);
        switch ($method)
        {
            case 'where':
                $this->Out .= " WHERE ";
                break;
            case 'str_contains':
            case 'str_starts_with':
            case 'str_ends_with':
                $this->Out .= " LIKE ";
                $this->Method = $expression->Method;
                break;
            case 'orderby':
            case 'orderbydescending':
                $this->Out .= " ORDER BY ";
                break;
            case 'thenby':
            case 'thenbydescending':
                $this->Out .= ", ";
                break;
            case 'groupby':
                $this->Out .= " GROUP BY ";
                break;
            default:
                # code...
                break;
        }
        
        foreach ($arguments as $argument)
        {
            $this->visit($argument);
        }

        if ($method == 'orderby' || $method == 'thenby')
        {
            $this->Out .= " ASC";
        } else if ($method == 'orderbydescending' || $method == 'thenbydescending') {
            $this->Out .= " DESC";
        }
    }

    public function visitArray(Expression $expression)
    {
        # code...
    }

    public function visitGroup(Expression $expression)
    {
        $this->Out .= "(";
        $this->visit($expression->Expression);
        $this->Out .= ")";
    }

    public function visitBinary(Expression $expression)
    {   
        $negation = '';
        switch ($expression->Name)
        {
            case '!=':
                $operator = '=';
                $negation = 'NOT ';
                break;
            case '==':
                $operator = '=';
                break;
            case '&&':
                $operator = 'AND';
                break;
            case '||':
                $operator = 'OR';
                break;
            default:
                $operator = $expression->Name;
                break;
        }
        $this->Out .= $negation;
        $this->visit($expression->Left);
        $this->Out .= ' '.$operator.' ';
        $this->visit($expression->Right);
    }

    public function visitProperty(Expression $expression)
    {
        if (in_array($expression->Parameter->Name, $this->Parameters))
        {
            $this->Out .= $expression->Property;
        }
        else
        {
            $this->Out .= '?';
        }
    }

    public function visitParameter(Expression $expression)
    {
        if (in_array($expression->Name, $this->Parameters))
        {
            $this->Out .= $expression->Name;
        }
        else
        {
            $this->OuterVariables[] = $expression->Value;
            $this->Out .= '?';
        }
    }

    public function visitConstant(Expression $expression)
    {
        if ($expression->Value instanceof IQueryable)
        {
            $this->Out .= "SELECT * FROM {$expression->Value->EntityType->getTableName()}";
        }
        else
        {
            switch ($this->Method) {
                case 'str_contains':
                    $this->Out .= "'%".$expression->Value."%'";
                    break;
                case 'str_starts_with':
                    $this->Out .= "{$expression->Value}%";
                    break;
                case 'str_ends_with':
                    $this->Out .= "%{$expression->Value}";
                    break;
                default:
                    if ($expression->Type == "string")
                    {
                        $this->Out .= "'{$expression->Value}'";
                        break;
                    }
                    else if ($expression->Type == "bool")
                    {
                        $this->Out .= $expression->Value == true ? "true" : "false";
                        break;
                    }
                    $this->Out .= "{$expression->Value}";
                    break;
            }
        }
    }

    public function visitUnary(Expression $expression)
    {
        $operator = $expression->Name;
        if ($expression->Name == '!')
        {
            $operator = 'NOT ';
        }
        $this->Out .= "{$operator}";
        $this->visit($expression->Operand);
    }

    public function __toString()
    {
        return $this->Out;
    }
}
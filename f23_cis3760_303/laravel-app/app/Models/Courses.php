<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

enum TokenType: string {
    case CODE = 'code';
    case OPEN_BRACKET = 'open_bracket';
    case CLOSE_BRACKET = 'close_bracket';
    case COMMA = 'comma';
    case OR = 'or';
    case X_OF = 'x or';
    case ARRAY = 'array';
}


class Courses extends Model
{
    protected $primaryKey = 'CourseID';
    public $timestamps = false;

    use HasFactory;
 
    public static function compilePrerequisites(string $prerequisites): array
    {
        $compiled = array();
        $temp = $prerequisites;

        while(strlen($temp) > 0) {
            $matches = array();
            $match_found = preg_match('/^[A-Z]{3,4}\*?[0-9]{4}\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::CODE,
                    "data" => trim($matches[0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $match_found = preg_match('/^[\(\[]\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::OPEN_BRACKET,
                    "data" => trim($matches[0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $match_found = preg_match('/^[\)\]]\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::CLOSE_BRACKET,
                    "data" => trim($matches[0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $match_found = preg_match('/^,\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::COMMA,
                    "data" => trim($matches[0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $match_found = preg_match('/^or\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::OR,
                    "data" => trim($matches[0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $match_found = preg_match('/^\d\s*of\s*/', $temp, $matches);
            if($match_found) {
                array_push($compiled, array(
                    "type" => TokenType::X_OF,
                    "data" => intval($matches[0][0])
                ));
                $temp = substr($temp, strlen($matches[0]));
                continue;
            }

            $temp = substr($temp, 1);
        }

        return $compiled;
    }

    public static function nestCompiled(array $compiled): array
    {
        $stack = array();
        $list = array();

        foreach($compiled as $token) {
            if(isset($token['type']) && $token['type'] == TokenType::OPEN_BRACKET) {
                array_push($stack, $list);
                $list = array();
            } else if(isset($token['type']) && $token['type'] == TokenType::CLOSE_BRACKET && count($stack) > 0) {
                $temp = array_pop($stack);
                array_push($temp, array(
                    "type" => TokenType::ARRAY,
                    "data" => $list
                ));
                $list = $temp;
            } else {
                array_push($list, $token);
            }
        }

        return $list;
    }

    public static function matchPrerequisites(array $compiled, array $student_courses): bool
    {
        if(isset($compiled[0]) && $compiled[0]['type'] == TokenType::X_OF) {
            $x = $compiled[0]['data'];
            $count = 0;

            foreach(array_slice($compiled, 1) as $token) {
                if(self::matchPrerequisites($token, $student_courses)) {
                    $count++;
                }
            }

            if($count >= $x) {
                return true;
            } else {
                return false;
            }
        }
        if(isset($compiled[1]) && $compiled[1]['type'] == TokenType::COMMA) {
            return
                self::matchPrerequisites($compiled[0], $student_courses) &&
                self::matchPrerequisites(array_slice($compiled, 2), $student_courses);
        }
        if(isset($compiled[1]) && $compiled[1]['type'] == TokenType::OR) {
            return
                self::matchPrerequisites($compiled[0], $student_courses) ||
                self::matchPrerequisites(array_slice($compiled, 2), $student_courses);
        }
        if((isset($compiled['type']) && $compiled['type'] == TokenType::ARRAY) || (isset($compiled[0]) && $compiled[0]['type'] == TokenType::ARRAY)) {
            return
                self::matchPrerequisites($compiled['data'] ?? $compiled[0]['data'], $student_courses);
        }
        if((isset($compiled['type']) && $compiled['type'] == TokenType::CODE) || (isset($compiled[0]) && $compiled[0]['type'] == TokenType::CODE)) {
            return
                in_array($compiled['data'] ?? $compiled[0]['data'], $student_courses);
        }

        return false;
    }
}

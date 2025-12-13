<?php

namespace App\Helpers;

use App\Core\Database;

class Validator
{
    private $errors = [];

    /**
     * Validate dữ liệu theo rules
     * @param array $data Dữ liệu đầu vào ($_POST)
     * @param array $rules Luật validate 
     * Ví dụ: ['email' => 'required|email|unique:users', 'password' => 'required|min:6']
     */
    public function validate($data, $rules)
    {
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramStr) = explode(':', $rule);
                    $params = explode(',', $paramStr);
                }

                $value = isset($data[$field]) ? trim($data[$field]) : '';

                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $this->addError($field, "Trường này là bắt buộc.");
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Email không hợp lệ.");
                        }
                        break;

                    case 'min':
                        if (strlen($value) < $params[0]) {
                            $this->addError($field, "Độ dài tối thiểu là {$params[0]} ký tự.");
                        }
                        break;

                    case 'max':
                        if (strlen($value) > $params[0]) {
                            $this->addError($field, "Độ dài tối đa là {$params[0]} ký tự.");
                        }
                        break;

                    case 'match': // match:password
                        $targetField = $params[0];
                        $targetValue = isset($data[$targetField]) ? trim($data[$targetField]) : '';
                        if ($value !== $targetValue) {
                            $this->addError($field, "Giá trị không khớp với trường {$targetField}.");
                        }
                        break;

                    case 'unique': // unique:table,column,except_id
                        $table = $params[0];
                        $column = isset($params[1]) ? $params[1] : $field;
                        $exceptId = isset($params[2]) ? $params[2] : null;

                        if ($this->checkExists($table, $column, $value, $exceptId)) {
                            $this->addError($field, "Giá trị này đã tồn tại trong hệ thống.");
                        }
                        break;
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function checkExists($table, $column, $value, $exceptId = null)
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value";
        if ($exceptId) {
            $sql .= " AND id != :except_id";
        }

        $db->query($sql);
        $db->bind(':value', $value);
        if ($exceptId) {
            $db->bind(':except_id', $exceptId);
        }

        $row = $db->single();
        return $row['count'] > 0;
    }
}

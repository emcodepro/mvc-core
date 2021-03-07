<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 10:57
 */

namespace emcodepro\mvc;


abstract class Model
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';
    const RULE_UNIQUE = 'unique';

    public $errors = [];

    abstract public function rules(): array;

    /**
     * @return array
     */
    public function labels(): array
    {
        return [];
    }

    //abstract public function tableName(): string;
    /**
     * @param $requestBody
     * @return bool
     */
    public function load($requestBody)
    {
        foreach ($requestBody as $key => $value){
            $this->{$key} = $value;
        }

        return true;
    }

    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules)
        {
            $value = $this->{$attribute};

            foreach ($rules as $rule){
                $ruleName = $rule;

                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }

                if($ruleName === self::RULE_REQUIRED && empty($value)){
                    $this->addError($attribute, $ruleName);
                }

                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addError($attribute, $ruleName);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addError($attribute, $ruleName, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
                    $this->addError($attribute, $ruleName, $rule);
                }

                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $this->addError($attribute, $ruleName, $rule);
                }

                if($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];
                    $tableName = $className::tableName();

                    $statement = Application::$app->db->pdo->prepare("SELECT * FROM $tableName WHERE $attribute = :$attribute");
                    $statement->bindValue(":$attribute", $value);
                    $statement->execute();
                    if($statement->fetchObject()){
                        $this->addError($attribute, $ruleName, ['field' => $this->getLabel($attribute) ?? $attribute]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function addError($attribute, $ruleName, $params = []){

        $message = $this->getErrorMessage()[$ruleName] ?? $ruleName;
        foreach ($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    public function getErrorMessage()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'This {field} has been taken by another user'
        ];
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? null;
    }

    public function hasError($attribute)
    {
        return !empty($this->errors[$attribute]);
    }

    public function getLabel($attribute){
        return $this->labels()[$attribute] ?? $attribute;
    }
}
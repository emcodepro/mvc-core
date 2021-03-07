<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 17:18
 */

namespace app\core\form;


class Field
{
    public $model;
    public $attribute;
    public $type = 'text';

    public function __construct($model, $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString()
    {
        return sprintf('   
                <div class="mb-3">
                     <label class="form-label">%s</label>
                     <input type="%s" name="%s" value="%s" class="form-control %s">
                     <div class="invalid-feedback">
                         %s
                     </div>
                 </div>',
            $this->model->getLabel($this->attribute),
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid': null,
            $this->model->getFirstError($this->attribute));
    }

    public function passwordInput()
    {
        $this->type = 'password';
        return $this;
    }

    public function emailInput()
    {
        $this->type = 'email';
        return $this;
    }
}
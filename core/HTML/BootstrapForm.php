<?php

namespace Core\HTML;

class BootstrapForm extends Form
{

    protected function surround($html)
    {
        return "<div class=\"form-group\">{$html}</div>";
    }
    public function submit()
    {
        return $this->surround('<button type="submit" class="btn btn-primary">Envoyer</button>');
    }


    public function input($name, $label, $options = [])
    {


        $type = isset($options['type']) ? $options['type'] : 'text';
        $disabled = isset($options['disable']) ? $options['disable'] : '';
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        if ($type === 'textarea') {
            $input = '<div class="col-sm-10"><textarea name="' . $name . '" minlength="'.$options['min'].'" maxlength="'.$options['max'].'" class="form-control" '. $disabled .'>' . htmlspecialchars($this->getValue($name)) .'</textarea></div>';
        } elseif ($type === 'date') {
            $input = '<div class="col-md-10"><input type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"  minlength="'.$options['min'].'" maxlength="'.$options['max'].'" class="form-control"' .$disabled.'></div>';
        } elseif ($type === 'number') {
            $input = '<div class="col-sm-10"><input type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"  minlength="'.$options['min'].'" maxlength="'.$options['max'].'" class="form-control" ' .$disabled.'></div>';
        } else {
            $input = '<div class="col-sm-10"><input type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"  minlength="'.$options['min'].'" maxlength="'.$options['max'].'" class="form-control" ' .$disabled.'></div>';
        }
        return $this->surround('<div class="form-group row">' . $label . $input . ' </div>');

    }

    public function inputNumber($name, $label, $min, $max, $options = [])
    {

        $type = isset($options['type']) ? $options['type'] : 'text';
        $disabled = isset($options['disable']) ? $options['disable'] : '';
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        $class = isset($options['class']) ? $options['class'] : '';
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        if ($type === 'number') {
            $input = '<div class="col-sm-10"><input placeholder="'.$placeholder.'" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" class="form-control '.$class.'" min="' . $min . '" max="' . $max . '" step="any" '.$disabled .'></div>';
        } else {
            $input = '<div class="col-sm-10"><input placeholder="'.$placeholder.'" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" class="form-control" '.$disabled .'></div>';
        }
        return $this->surround('<div class="form-group row">' . $label . $input . ' </div>');

    }

    public function inputD($name, $label, $options = [])
    {
        $type = isset($options['type']) ? $options['type'] : 'text';
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $class = isset($options['class']) ? $options['class'] : '';
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';

        if ($type === 'textarea') {
            $input = '<div class="col-sm-10"><textarea name="' . $name . '"  class="form-control" disabled="disabled">' . htmlspecialchars($this->getValue($name)) . '</textarea></div>';
        } elseif ($type === 'date') {
            $input = '<div class="col-sm-10"><input type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" class="form-control" disabled="disabled"></div>';
        } elseif ($type === 'date') {
            $input = '<div class="col-sm-10"><input type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" class="form-control" disabled="disabled"></div>';
        } else {
            $input = '<div class="col-sm-10"><input placeholder = "'.$placeholder.'"  type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" class="form-control '.$class.'" disabled="disabled"></div>';
        }
        return $this->surround('<div class="form-group row">' . $label . $input . ' </div>');
    }

    public function inputWithId($id, $name, $label,$min, $max,  $options = [])
    {
        $type = isset($options['type']) ? $options['type'] : 'text';
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        if ($type === 'textarea') {
            $input = '<div class="col-sm-10"><textarea id="' . $id . '" name="' . $name . '"  minlength="'.$min.'" maxlength="'.$max.'" class="form-control">' . htmlspecialchars($this->getValue($name)) . '</textarea></div>';
        } elseif ($type === 'date') {
            $input = '<div class="col-sm-10"><input type="' . $type . '" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"   minlength="'.$min.'" maxlength="'.$max.'" class="form-control"></div>';
        } elseif ($type === 'date') {
            $input = '<div class="col-sm-10"><input type="' . $type . '" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"   minlength="'.$min.'" maxlength="'.$max.'" class="form-control"></div>';
        } else {
            $input = '<div class="col-sm-10"><input type="' . $type . '" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"   minlength="'.$min.'" maxlength="'.$max.'" class="form-control"></div>';
        }
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }



    public function inputWithIdAutocomp($id, $name, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '"  minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocomp" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }

    public function inputWithIdAutocompEditPost($id, $name, $value, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocomp" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }

    public function inputWithIdAutocompD($id, $name, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocomp" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '" disabled="disabled"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }

    public function inputWithIdAutocompEditPostD($id, $name, $value, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocomp" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '" disabled="disabled"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');


    }




    public function inputWithIdAutocompPrincipal($id, $name, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocompPrincipal" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }

    public function inputWithIdAutocompEditPostPrincipal($id, $name, $value, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocompPrincipal" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');
    }

    public function inputWithIdAutocompDPrincipal($id, $name, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($this->getValue($name)) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocompPrincipal" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '" disabled="disabled"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }

    public function inputWithIdAutocompEditPostDPrincipal($id, $name, $value, $label, $conf, $champs, $confName, $alias, $min, $max, $key)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><input type="text" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" minlength="'.$min.'" maxlength="'.$max.'" class="form-control autocompPrincipal" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-alias="'.$alias.'" data-key="' . $key . '" disabled="disabled"></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');

    }


    public function select($name, $label, $options)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><select class="form-control" name="' . $name . '">';
        foreach ($options as $k => $v) {
            $attributes = '';
            if ($k == htmlspecialchars($this->getValue($name))) {
                $attributes = 'selected';
            }

            $input .= "<option value='$k'$attributes>$v</option>";
        }
        $input .= '</select></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');
    }

    public function selectWithId($id, $name, $label, $options)
    {
        $label = '<label class="col-sm-2 col-form-label">' . $label . '</label>';
        $input = '<div class="col-sm-10"><select class="form-control" id="' . $id . '" name="' . $name . '" onchange=\'go()\'>';
        foreach ($options as $k => $v) {
            $attributes = '';
            if ($k == htmlspecialchars($this->getValue($name))) {
                $attributes = 'selected';
            }

            $input .= "<option value='$k'$attributes>$v</option>";
        }
        $input .= '</select></div>';
        return $this->surround('<div class="form-group row">' . $label . $input . '</div>');
    }

    public function selectFilter($name, $default, $options)
    {

        $input = '<select id="selectFilter" class="form-control" name="' . $name . '">
        <option value="0">' . $default . '</option>';
        foreach ($options as $k => $v) {
            $attributes = '';
            if ($k == htmlspecialchars($this->getValue($name))) {
                $attributes = 'selected';
            }
            $input .= "<option value='$k'$attributes>$v</option>";
        }
        $input .= '</select>';
        return $this->surround($input);
    }

    public function inputWithIdAutocompPrincipalInputMultiple($id, $name, $conf, $champs, $confName, $key)
    {
        $input = '<input placeholder="Saisir le site :" type="text" id="' . $id . '" name="' . $name . '" class="form-control autocompSecto" data-conf="' . $conf . '" data-champs="' . $champs . '" data-confName="' . $confName . '" data-key="' . $key . '">';
        return $this->surround($input);
    }

}

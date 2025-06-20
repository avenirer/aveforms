<?php
class AveValidator
{
    private $ruleSet = [];
    private $messages = [];
    private $labels = [];

    public function setRules(array $ruleSet)
    {
        $this->ruleSet = $ruleSet;
        return $this;
    }

    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    public function setLabels(array $labels)
    {
        $this->labels = $labels;
        return $this;
    }

    public function validate(array $data)
    {
        foreach($this->ruleSet as $field => $rules) {
            if(is_string($rules)) {
                $rules = explode('|', $rules);
            }

            if (in_array('required', $rules) && !isset($data[$field])) {
                $this->errors[$field]['required'] = $this->messages["{$field}.required"] ?? ($this->labels[$field] ?? ucfirst($field)) . ' is required.';
            }
            
            if (isset($data[$field])) {
                $value = $data[$field];
                foreach ($rules as $rule) {
                    if ($rule === 'required') {
                        continue; // Skip 'required' check here, already handled above
                    }
                    switch ($rule) {
                        case 'string':
                            if (!is_string($value)) {
                                $this->errors[$field]['string'] = $this->messages["{$field}.string"] ?? ($this->labels[$field] ?? ucfirst($field)) . ' must be a string.';
                            }
                            break;
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->errors[$field]['email'] = $this->messages["{$field}.email"] ?? ($this->labels[$field] ?? ucfirst($field)) . ' must be a valid email address.';
                            }
                            break;
                        case 'integer':
                            if (!is_int($value)) {
                                $this->errors[$field]['integer'] = $this->messages["{$field}.integer"] ?? ($this->labels[$field] ?? ucfirst($field)) . ' must be an integer.';
                            }
                            break;
                        default:
                            if (preg_match('/min:(\d+)/', $rule, $matches) && (is_int($value) && $value < (int)$matches[1])) {
                                $this->errors[$field]['min'] = $this->messages["{$field}.min"] ?? ($this->labels[$field] ?? ucfirst($field)) . " must be at least {$matches[1]}.";
                            } elseif (preg_match('/max:(\d+)/', $rule, $matches) && is_int($value) && $value > (int)$matches[1]) {
                                $this->errors[$field]['max'] = $this->messages["{$field}.max"] ?? ($this->labels[$field] ?? ucfirst($field)) . " must not exceed {$matches[1]}.";
                            }
                            if (preg_match('/min:(\d+)/', $rule, $matches) && (is_string($value) && strlen($value) < (int)$matches[1])) {
                                $this->errors[$field]['min'] = $this->messages["{$field}.min"] ?? ($this->labels[$field] ?? ucfirst($field)) . " must be at least {$matches[1]} characters long.";
                            } elseif (preg_match('/max:(\d+)/', $rule, $matches) && is_string($value) && strlen($value) > (int)$matches[1]) {
                                $this->errors[$field]['max'] = $this->messages["{$field}.max"] ?? ($this->labels[$field] ?? ucfirst($field)) . " must not exceed {$matches[1]} characters.";
                            }
                            break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors ?? [];
    }

    public function getValues($sanitized = false)
    {
        if ($sanitized) {
            return array_map('htmlspecialchars', $this->data);
        }
        return $this->data;
    }
}
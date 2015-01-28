<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing\objects;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Type
 */
class Type extends \yii\base\Object
{
    /**
     * @var string
     */
    public $reaplyTo;
    /**
     * @var string
     */
    public $label = "Not set";
    /**
     * @var string
     */
    public $emailKey = "email";
    /**
     * @var \Closure
     */
    private $_query;
    /**
     * @var \Closure
     */
    private $_var;

    /**
     * @param $row
     * @return array
     */
    public function getVarTemplate($row)
    {
        $replace = call_user_func($this->getVar(), $row);
        if (!is_array($replace)) {
            throw new InvalidConfigException('The "var" property must be Closure and return array.');
        }
        return $replace;
    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * @inheritdoc
     * @param $value
     * @throws InvalidConfigException
     */
    public function setQuery($value)
    {
        if (!($value instanceof \Closure)) {
            throw new InvalidConfigException('The "query" property must be Closure.');
        }
        $this->_query = $value;
    }

    /**
     * @inheritdoc
     * @return \Closure
     */
    public function getVar()
    {
        if ($this->_var === null) {
            $this->setVar(function ($row) {
                $replace = [];
                foreach ($row as $name => $value) {
                    $replace['{' . $name . '}'] = $value;
                }
                return $replace;
            });
        }
        return $this->_var;
    }

    /**
     * @inheritdoc
     * @param $value
     * @throws InvalidConfigException
     */
    public function setVar($value)
    {
        if (!($value instanceof \Closure)) {
            throw new InvalidConfigException('The "var" property must be Closure.');
        }
        $this->_var = $value;
    }


}
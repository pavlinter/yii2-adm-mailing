<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing;

use Closure;
use pavlinter\adm\Adm;
use pavlinter\adm\AdmBootstrapInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * @property \pavlinter\admmailing\ModelManager $manager
 */
class Module extends \yii\base\Module implements AdmBootstrapInterface
{
    public $controllerNamespace = 'pavlinter\admmailing\controllers';

    public $layout = '@vendor/pavlinter/yii2-adm/adm/views/layouts/main';
    /**
     * @var array|Closure
     * example:
     * [
     *   'user' => function(){ return \pavlinter\adm\models\User::find(); },
     * ]
     * OR
     * [
     *   'user' => [
     *      'func' => function(){ return \pavlinter\adm\models\User::find(); }
     *      'label' => 'myLabel'
     *   ],
     * ]
     */
    public $typeList;
    /**
     * @var array
     * example:
     * [
     *   [
     *      'email' => 'test@test.com',
     *      'name' => 'myfromName',
     *   ],
     *   [
     *      'email' => 'test2@test.com',
     *      'name' => 'myfromName2',
     *   ]
     * ]
     */
    public $from = [];
    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'manager' => [
                    'class' => 'pavlinter\admmailing\ModelManager'
                ],
            ],
        ], $config);

        parent::__construct($id, $parent, $config);
    }

    public function init()
    {
        parent::init();
        $this->setTypeList();
    }

    /**
     * @param \pavlinter\adm\Adm $adm
     */
    public function loading($adm)
    {
        if ($adm->user->can('Adm-Mailing')) {
            $adm->params['left-menu']['admmailing'] = [
                'label' => '<i class="fa fa-envelope"></i><span>' . $adm::t('menu', 'Mailing') . '</span>',
                'url' => ['/admmailing/mailing/index']
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $adm = Adm::register();
        if (!parent::beforeAction($action) || !$adm->user->can('Adm-Mailing')) {
            return false;
        }
        return true;
    }

    public function setFrom()
    {
        if ($this->from instanceof Closure) {
            $this->from = call_user_func($this->from, $this);
        }

        if (!is_array($this->from)) {
            throw new InvalidConfigException('The "from" property must be array.');
        }

        foreach ($this->from as $key => $options) {
            if (!is_array($options)) {
                $options = [
                    'email' => $options,
                ];
            }

            if (!isset($options['email'])) {
                throw new InvalidConfigException('The "from" property must be correct structure.');
            }

            if (isset($options['name'])) {
                $options['emailName'] = [$options['email'] => $options['name']];
            } else {
                $options['emailName'] = $options['email'];
            }

            $this->from[$key] = $options;
        }

        if (empty($this->from)) {
            throw new InvalidConfigException('The "from" property must be at least one element.');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function setTypeList()
    {
        if ($this->typeList instanceof Closure) {
            $this->typeList = call_user_func($this->typeList, $this);
        }

        if (!is_array($this->typeList)) {
            throw new InvalidConfigException('The "typeList" property must be array.');
        }

        if (empty($this->typeList)) {
            throw new InvalidConfigException('The "typeList" property must be at least one element.');
        }

        foreach ($this->typeList as $key => $value) {
            if ($value instanceof Closure) {
                $options = [
                    'func' => $value
                ];
            } else if (is_array($value)) {
                $options = $value;
            } else {
                throw new InvalidConfigException('The "typeList" property must be correct structure.');
            }

            $this->typeList[$key] = ArrayHelper::merge([
                'func' => $value,
                'label' => $key,
            ], $options);
        }
    }
}

<?php

namespace pavlinter\admmailing\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%mailing}}".
 *
 * @method \pavlinter\translation\TranslationBehavior getLangModels
 * @method \pavlinter\translation\TranslationBehavior setLanguage
 * @method \pavlinter\translation\TranslationBehavior getLanguage
 * @method \pavlinter\translation\TranslationBehavior saveTranslation
 * @method \pavlinter\translation\TranslationBehavior saveAllTranslation
 * @method \pavlinter\translation\TranslationBehavior saveAll
 * @method \pavlinter\translation\TranslationBehavior validateAll
 * @method \pavlinter\translation\TranslationBehavior validateLangs
 * @method \pavlinter\translation\TranslationBehavior loadAll
 * @method \pavlinter\translation\TranslationBehavior loadLang
 * @method \pavlinter\translation\TranslationBehavior loadLangs
 * @method \pavlinter\translation\TranslationBehavior getTranslation
 * @method \pavlinter\translation\TranslationBehavior hasTranslation
 *
 * @property integer $id
 * @property string $title
 * @property string $email
 * @property string $name
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $subject
 * @property string $text
 *
 * @property MailingLang[] $translations
 */
class Mailing extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
			[
				'class' => \yii\behaviors\TimestampBehavior::className(),
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => 'updated_at',
				'attributes' => [
					\yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
					\yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
				], 
				'value' => new \yii\db\Expression('NOW()')
			],
            'trans' => [
                'class' => \pavlinter\translation\TranslationBehavior::className(),
                'translationAttributes' => [
                    'subject',
                    'text',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailing}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['email'], 'email'],
            [['title', 'email', 'name'], 'string', 'max' => 250],
            [['type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modelAdm/adm_mailing', 'ID'),
            'title' => Yii::t('modelAdm/adm_mailing', 'Title'),
            'email' => Yii::t('modelAdm/adm_mailing', 'Email'),
            'name' => Yii::t('modelAdm/adm_mailing', 'Name'),
            'type' => Yii::t('modelAdm/adm_mailing', 'Type'),
            'created_at' => Yii::t('modelAdm/adm_mailing', 'Created At'),
            'updated_at' => Yii::t('modelAdm/adm_mailing', 'Updated At'),
        ];
    }

    public static function typeList($type = false)
    {
        $module = Yii::$app->getModule('admmailing');
        if ($type !== false) {
            if (isset($module->typeList[$type])) {
                return $module->typeList[$type]->label;
            }
            return null;
        }
        return ArrayHelper::getColumn($module->typeList, 'label');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        /* @var \pavlinter\admmailing\Module $module */
        $module = Yii::$app->getModule('admmailing');
        return $this->hasMany($module->manager->mailingLangClass, ['mailing_id' => 'id']);
    }
}

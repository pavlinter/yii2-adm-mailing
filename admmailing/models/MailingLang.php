<?php

namespace pavlinter\admmailing\models;

use Yii;

/**
 * This is the model class for table "{{%mailing_lang}}".
 *
 * @property integer $id
 * @property integer $mailing_id
 * @property integer $language_id
 * @property string $subject
 * @property string $text
 *
 * @property Language $language
 * @property Mailing $mailing
 */
class MailingLang extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailing_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mailing_id', 'language_id'], 'integer'],
            [['text'], 'string'],
            [['subject'], 'string', 'max' => 100]
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
            'id' => Yii::t('modelAdm/adm_mailing_lang', 'ID'),
            'mailing_id' => Yii::t('modelAdm/adm_mailing_lang', 'Mailing ID'),
            'language_id' => Yii::t('modelAdm/adm_mailing_lang', 'Language ID'),
            'subject' => Yii::t('modelAdm/adm_mailing_lang', 'Subject'),
            'text' => Yii::t('modelAdm/adm_mailing_lang', 'Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailing()
    {
        return $this->hasOne(Mailing::className(), ['id' => 'mailing_id']);
    }
}

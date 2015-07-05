<?php

namespace demi\comments\common\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "comments".
 *
 * @property string $id
 * @property integer $material_type
 * @property string $material_id
 * @property string $text
 * @property string $user_id
 * @property string $user_name
 * @property string $user_email
 * @property string $user_ip
 * @property string $parent_id
 * @property integer $language_id
 * @property integer $is_replied
 * @property integer $is_approved
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * RELATIONS
 * @property \common\models\User $user
 *
 * GETTERS
 * comment user info
 * @property string|null $userProfileUrl
 * @property string|null $userPhoto
 * @property string|null $username
 * comment data
 * @property string $preparedText
 * @property string $fDate
 * @property bool $isAnonymous
 * others
 * @property \demi\comments\common\components\Comment|null $component
 */
class Comment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{comments}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['default'] = ['text', 'user_name', 'user_email', 'parent_id'];
        $scenarios['admin'] = array_merge($scenarios['default'], [
            'material_type', 'material_id', 'user_id', 'user_ip', 'language_id', 'is_approved', 'is_deleted',
            'created_at', 'updated_at',
        ]);

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['material_type', 'material_id', 'text'], 'required'],
            // integer
            [['material_type', 'material_id', 'user_id', 'parent_id', 'language_id'], 'integer'],
            // boolean
            [['is_replied', 'is_approved', 'is_deleted'], 'boolean'],
            // string
            [['text'], 'string'],
            // string max
            [['user_name', 'user_email'], 'string', 'max' => 255],
            // email
            [['user_email'], 'email', 'allowEmpty' => true],
            // default
            [['is_replied', 'is_approved', 'is_deleted'], 'default', 'value' => 0],
            [['user_id'], 'default', 'value' => Yii::$app->has('user') ? Yii::$app->user->id : null],
            [['user_ip'], 'default', 'value' => new Expression('INET_ATON(:userIP)', [':userIP' => Yii::$app->request->userIP])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_type' => 'Material Type',
            'material_id' => 'Material ID',
            'text' => 'Text',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'user_ip' => 'User IP',
            'parent_id' => 'Parent ID',
            'language_id' => 'Language ID',
            'is_replied' => 'Is Replied',
            'is_approved' => 'Is Approved',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * User relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        $component = static::getComponent();

        return $this->hasOne($component->userModelClass, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => new Expression('NOW()'),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $normalize = function ($string) {
            if (!is_string($string)) {
                return null;
            }

            return trim(preg_replace('/[\s]+/iu', ' ', $string));
        };
        // Trim user name & email and replace 2+ spaces to 1
        $this->user_name = $normalize($this->user_name);
        $this->user_email = $normalize($this->user_email);

        if ($this->isNewRecord) {
            $this->user_id = Yii::$app->has('user') ? Yii::$app->user->id : null;
        }

        if (empty($this->user_id)) {
            if (empty($this->user_name)) {
                $this->addError('user_name', 'You must set you name');
            }
            if (empty($this->user_email)) {
                $this->addError('user_email', 'You must set you email');
            }
        } else {
            $this->user_name = $this->user_email = null;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->user_ip = new Expression('INET_ATON(:userIP)', [':userIP' => Yii::$app->request->userIP]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // something...

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Do not forget delete the related data!

        return true;
    }

    /**
     * Get comment component
     *
     * @param string $name Name of comment component
     *
     * @throws \yii\base\Exception
     * @return \demi\comments\common\components\Comment|null
     */
    public static function getComponent($name = 'comment')
    {
        if (Yii::$app->has($name)) {
            return Yii::$app->get($name);
        }

        throw new Exception('Component "' . $name . '" was not found');
    }

    /**
     * Get current comment user profile url
     *
     * @return mixed|null
     */
    public function getUserProfileUrl()
    {
        $func = static::getComponent()->getUserProfileUrl;

        return is_callable($func) ? call_user_func($func, $this) : null;
    }

    /**
     * Get current comment user photo
     *
     * @return mixed|null
     */
    public function getUserPhoto()
    {
        $func = static::getComponent()->getUserPhoto;

        return is_callable($func) ? call_user_func($func, $this) : null;
    }

    /**
     * Get current comment username
     *
     * @return mixed|null
     */
    public function getUsername()
    {
        $func = static::getComponent()->getUsername;

        return is_callable($func) ? call_user_func($func, $this) : $this->user_name;
    }

    /**
     * Get HTML-encoded comment text
     *
     * @return string
     */
    public function getPreparedText()
    {
        $func = static::getComponent()->getCommentText;

        return is_callable($func) ? call_user_func($func, $this) : nl2br(\yii\helpers\Html::encode($this->text));
    }

    /**
     * Get formatted comment create time
     *
     * @return string
     */
    public function getFDate()
    {
        $func = static::getComponent()->getCommentDate;

        return is_callable($func) ? call_user_func($func, $this) : Yii::$app->formatter->asDate($this->created_at);
    }

    /**
     * Checks that this comment sent from anonym
     *
     * @return bool
     */
    public function getIsAnonymous()
    {
        return empty($this->user_id);
    }
}

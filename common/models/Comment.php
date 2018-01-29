<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\common\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use demi\comments\common\components\CommentQuery;

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
 * @property-read \common\models\User $user
 * @property-read \demi\comments\common\models\Comment $parent
 *
 * GETTERS
 * comment user info
 * @property-read string|null $userProfileUrl
 * @property-read string|null $userPhoto
 * @property-read string|null $username
 * @property-read string $permalink
 * comment data
 * @property-read string $preparedText
 * @property-read string $fDate
 * @property-read bool $isAnonymous
 * others
 * @property-read \demi\comments\common\components\Comment $component
 */
class Comment extends ActiveRecord
{
    public $captcha;

    /**
     * @inheritdoc
     *
     * @return CommentQuery|ActiveQuery
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

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
        $scenarios['default'] = ['material_type', 'material_id', 'text', 'user_name', 'user_email', 'parent_id', 'captcha'];
        $scenarios['admin'] = array_merge($scenarios['default'], [
            'user_id', 'user_ip', 'language_id', 'is_approved', 'is_deleted',
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
            [
                ['captcha', 'user_name', 'user_email'], 'required',
                'when' => function ($model) {
                    /** @var $model self */
                    return Yii::$app->has('user') && $model->isNewRecord && Yii::$app->user->isGuest;
                }
            ],
            // integer
            [['material_type', 'material_id', 'user_id', 'parent_id', 'language_id'], 'integer'],
            // boolean
            [['is_replied', 'is_approved', 'is_deleted'], 'boolean'],
            // string
            [['text'], 'string'],
            // string max
            [['user_name', 'user_email'], 'string', 'max' => 255],
            // email
            [['user_email'], 'email'],
            // exists
            [['parent_id'], 'exist', 'targetAttribute' => 'id'],
            // default
            [['is_replied', 'is_approved', 'is_deleted'], 'default', 'value' => 0],
            // captcha
            [
                ['captcha'], 'demi\recaptcha\ReCaptchaValidator', 'secretKey' => $this->component->reCaptchaSecretKey,
                'when' => function ($model) {
                    /** @var $model self */
                    return Yii::$app->has('user') && $model->isNewRecord && !$model->hasErrors() && Yii::$app->user->isGuest;
                }
            ],
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
            'text' => 'Comment',
            'user_id' => 'User ID',
            'user_name' => 'Name',
            'user_email' => 'Email',
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
     * Parent comment relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(get_class($this), ['id' => 'parent_id']);
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

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        // some...

        parent::afterValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            // Default user ID
            $this->user_id = Yii::$app->has('user') ? Yii::$app->user->id : null;

            // Default user IP
            $this->user_ip = new Expression('INET_ATON(:userIP)', [':userIP' => Yii::$app->request->userIP]);

            if (!empty($this->user_id)) {
                // Clear name & email if user_id exists
                $this->user_name = $this->user_email = null;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->parent_id)) {
            // Set "is_replied"=1 for parent comment
            $this->updateAll(['is_replied' => 1], ['id' => $this->parent_id]);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        // If have at least one child comment
        if ($this->is_replied) {
            // Delete child comments
            $childs = $this->findAll(['parent_id' => $this->id]);
            foreach ($childs as $child) {
                $child->delete();
            }
        }

        if (!empty($this->parent_id)) {
            // Set "is_replied"=0 for parent comment
            $this->updateAll(['is_replied' => 0], ['id' => $this->parent_id]);
        }

        parent::afterDelete();
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
    public function getComponent($name = 'comment')
    {
        return Yii::$app->get($name);
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

        return is_callable($func) ? call_user_func($func, $this) : nl2br(\yii\helpers\Html::encode(trim($this->text)));
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
     * Get permanent link for this comment
     *
     * @return mixed|string
     */
    public function getPermalink()
    {
        $func = static::getComponent()->getPermalink;

        return is_callable($func) ? call_user_func($func, $this) : '#comment-' . $this->id;
    }

    /**
     * Checks that the current user can update this comment
     *
     * @return bool
     */
    public function canUpdate()
    {
        $func = static::getComponent()->canUpdate;

        return is_callable($func) ? call_user_func($func, $this) : $func;
    }

    /**
     * Checks that the current user can delete this comment
     *
     * @return bool
     */
    public function canDelete()
    {
        $func = static::getComponent()->canDelete;

        return is_callable($func) ? call_user_func($func, $this) : $func;
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

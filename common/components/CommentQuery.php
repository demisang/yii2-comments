<?php

namespace demi\comments\common\components;

use yii\db\ActiveQuery;

class CommentQuery extends ActiveQuery
{
    /**
     * Add general conditions to search
     *
     * @return static
     */
    public function general()
    {
        $select = [
            'publications.id',
            'publications.type',
            'publications.title',
            'publications.image',
            'publications.short_description',
            'publications.seo_url',
            'publications.date',
        ];

        $this->orderBy = ['publications.date' => SORT_DESC];

        return $this->select($select);
    }

    /**
     * Find only visible publications
     *
     * @param bool $state
     *
     * @return static
     */
    public function active($state = true)
    {
        return $this->andWhere(['status' => $state ? PublicationHelper::STATUS_ACTIVE : PublicationHelper::STATUS_INACTIVE]);
    }
}
<?php

class Announcements extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'Announ';

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws SoftException
     */
    public static function Add($subj, $fromid, $text)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values([
                'subj' => '?',
                'fromid' => '?',
                'text' => '?',
                'time' => '?',
            ])
            ->setParameter(0, $subj)
            ->setParameter(1, $fromid)
            ->setParameter(2, $text)
            ->setParameter(3, time())
            ->execute();
        $u = new User($fromid);
        //Insert into forum_topics
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('forum_topics')
            ->values([
                'parent_board' => '6',
                'name' => '?',
                'subject' => '?',
                'poster_id' => '?',

            ])
            ->setParameter(1, $subj)
            ->setParameter(2, $subj)
            ->setParameter(3, $u->id)
            ->execute();
        $topicid = $queryBuilder->getConnection()->lastInsertId();
        $queryBuilder = BaseObject::createQueryBuilder();
        //insert into forum_posts
        $queryBuilder
            ->insert('forum_posts')
            ->values([
                'parent_topic' => '?',
                'poster_id' => '?',
                'content' => '?',
                'announcement' => '1',

            ])
            ->setParameter(0, $topicid)
            ->setParameter(1, $u->id)
            ->setParameter(2, $text)

            ->execute();

    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'fromid',
            'text',
            'subj',
            'time',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

}
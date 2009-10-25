<?php

/**
 * BaseQoSessions
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $id
 * @property integer $qo_members_id
 * @property integer $qo_groups_id
 * @property string $ip
 * @property timestamp $date
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseQoSessions extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('qo_sessions');
        $this->hasColumn('id', 'string', 128, array(
             'type' => 'string',
             'length' => 128,
             'fixed' => false,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('qo_members_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 1,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('qo_groups_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 1,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('ip', 'string', 16, array(
             'type' => 'string',
             'length' => 16,
             'fixed' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('date', 'timestamp', null, array(
             'type' => 'timestamp',
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

}
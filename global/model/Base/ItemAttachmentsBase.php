<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * ItemAttachments
 *  This class has been auto-generated at 19/06/2013 18:40:11
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property integer $item_id item_id type : int(11)
 * @property string $file file type : text max_length : 
 * @property string $file_name file_name type : varchar(255) max_length : 255
 * @property string $mime_type mime_type type : text max_length : 
 * @property string $type_group type_group type : varchar(100) max_length : 100
 * @property datetime $uploaded_time uploaded_time type : datetime
 * @property integer $hits hits type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \ItemAttachments[] findById(integer $id) find objects in database by id
 * @method static \ItemAttachments findOneById(integer $id) find object in database by id
 * @method static \ItemAttachments retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll

 * @method void setItemId(integer $item_id) set item_id value
 * @method integer getItemId() get item_id value
 * @method static \ItemAttachments[] findByItemId(integer $item_id) find objects in database by item_id
 * @method static \ItemAttachments findOneByItemId(integer $item_id) find object in database by item_id
 * @method static \ItemAttachments retrieveByItemId(integer $item_id) retrieve object from poll by item_id, get it from db if not exist in poll

 * @method void setFile(string $file) set file value
 * @method string getFile() get file value
 * @method static \ItemAttachments[] findByFile(string $file) find objects in database by file
 * @method static \ItemAttachments findOneByFile(string $file) find object in database by file
 * @method static \ItemAttachments retrieveByFile(string $file) retrieve object from poll by file, get it from db if not exist in poll

 * @method void setFileName(string $file_name) set file_name value
 * @method string getFileName() get file_name value
 * @method static \ItemAttachments[] findByFileName(string $file_name) find objects in database by file_name
 * @method static \ItemAttachments findOneByFileName(string $file_name) find object in database by file_name
 * @method static \ItemAttachments retrieveByFileName(string $file_name) retrieve object from poll by file_name, get it from db if not exist in poll

 * @method void setMimeType(string $mime_type) set mime_type value
 * @method string getMimeType() get mime_type value
 * @method static \ItemAttachments[] findByMimeType(string $mime_type) find objects in database by mime_type
 * @method static \ItemAttachments findOneByMimeType(string $mime_type) find object in database by mime_type
 * @method static \ItemAttachments retrieveByMimeType(string $mime_type) retrieve object from poll by mime_type, get it from db if not exist in poll

 * @method void setTypeGroup(string $type_group) set type_group value
 * @method string getTypeGroup() get type_group value
 * @method static \ItemAttachments[] findByTypeGroup(string $type_group) find objects in database by type_group
 * @method static \ItemAttachments findOneByTypeGroup(string $type_group) find object in database by type_group
 * @method static \ItemAttachments retrieveByTypeGroup(string $type_group) retrieve object from poll by type_group, get it from db if not exist in poll

 * @method void setUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) setUploadedTime(string $uploaded_time) set uploaded_time value
 * @method \Flywheel\Db\Type\DateTime getUploadedTime() get uploaded_time value
 * @method static \ItemAttachments[] findByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) findByUploadedTime(string $uploaded_time) find objects in database by uploaded_time
 * @method static \ItemAttachments findOneByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) findOneByUploadedTime(string $uploaded_time) find object in database by uploaded_time
 * @method static \ItemAttachments retrieveByUploadedTime(\Flywheel\Db\Type\DateTime $uploaded_time) retrieveByUploadedTime(string $uploaded_time) retrieve object from poll by uploaded_time, get it from db if not exist in poll

 * @method void setHits(integer $hits) set hits value
 * @method integer getHits() get hits value
 * @method static \ItemAttachments[] findByHits(integer $hits) find objects in database by hits
 * @method static \ItemAttachments findOneByHits(integer $hits) find object in database by hits
 * @method static \ItemAttachments retrieveByHits(integer $hits) retrieve object from poll by hits, get it from db if not exist in poll


 */
abstract class ItemAttachmentsBase extends ActiveRecord {
    protected static $_tableName = 'item_attachments';
    protected static $_phpName = 'ItemAttachments';
    protected static $_pk = 'id';
    protected static $_alias = 'i';
    protected static $_dbConnectName = 'item_attachments';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'not_null' => true,
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'item_id' => array('name' => 'item_id',
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'file' => array('name' => 'file',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'file_name' => array('name' => 'file_name',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'mime_type' => array('name' => 'mime_type',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'text'),
        'type_group' => array('name' => 'type_group',
                'not_null' => true,
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'uploaded_time' => array('name' => 'uploaded_time',
                'not_null' => true,
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'hits' => array('name' => 'hits',
                'default' => 0,
                'not_null' => true,
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
     );
    protected static $_validate = array(
    );
    protected static $_init = false;
    protected static $_cols = array('id','item_id','file','file_name','mime_type','type_group','uploaded_time','hits');

    public function setTableDefinition() {
    }

    /**
     * save object model
     * @return boolean
     * @throws \Exception
     */
    public function save() {
        $conn = Manager::getConnection(self::getDbConnectName());
        $conn->beginTransaction();
        try {
            $this->_beforeSave();
            $status = $this->saveToDb();
            $this->_afterSave();
            $conn->commit();
            self::addInstanceToPool($this, $this->getPkValue());
            return $status;
        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * delete object model
     * @return boolean
     * @throws \Exception
     */
    public function delete() {
        $conn = Manager::getConnection(self::getDbConnectName());
        try {
            $this->_beforeDelete();
            $this->deleteFromDb();
            $this->_afterDelete();
            $conn->commit();
            self::removeInstanceFromPool($this->getPkValue());
            return true;
        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
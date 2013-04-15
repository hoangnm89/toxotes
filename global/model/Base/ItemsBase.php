<?php 
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
/**.
 * Items
 *  This class has been auto-generated at 15/04/2013 11:54:17
 * @version		$Id$
 * @package		Model

 * @property integer $id id primary auto_increment type : int(11) unsigned
 * @property string $title title type : varchar(255) max_length : 255
 * @property string $slug slug type : varchar(255) max_length : 255
 * @property string $excerpt excerpt type : text max_length : 
 * @property string $content content type : text max_length : 
 * @property string $status status type : varchar(20) max_length : 20
 * @property string $author author type : varchar(255) max_length : 255
 * @property string $taxonomy taxonomy type : varchar(100) max_length : 100
 * @property string $language language type : varchar(20) max_length : 20
 * @property datetime $modified_time modified_time type : datetime
 * @property datetime $created_time created_time type : datetime
 * @property integer $ordering ordering type : int(11)
 * @property integer $hits hits type : int(11)

 * @method void setId(integer $id) set id value
 * @method integer getId() get id value
 * @method static \Items[] findById(integer $id) find objects in database by id
 * @method static \Items findOneById(integer $id) find object in database by id
 * @method static \Items retrieveById(integer $id) retrieve object from poll by id, get it from db if not exist in poll
 * @method void setTitle(string $title) set title value
 * @method string getTitle() get title value
 * @method static \Items[] findByTitle(string $title) find objects in database by title
 * @method static \Items findOneByTitle(string $title) find object in database by title
 * @method static \Items retrieveByTitle(string $title) retrieve object from poll by title, get it from db if not exist in poll
 * @method void setSlug(string $slug) set slug value
 * @method string getSlug() get slug value
 * @method static \Items[] findBySlug(string $slug) find objects in database by slug
 * @method static \Items findOneBySlug(string $slug) find object in database by slug
 * @method static \Items retrieveBySlug(string $slug) retrieve object from poll by slug, get it from db if not exist in poll
 * @method void setExcerpt(string $excerpt) set excerpt value
 * @method string getExcerpt() get excerpt value
 * @method static \Items[] findByExcerpt(string $excerpt) find objects in database by excerpt
 * @method static \Items findOneByExcerpt(string $excerpt) find object in database by excerpt
 * @method static \Items retrieveByExcerpt(string $excerpt) retrieve object from poll by excerpt, get it from db if not exist in poll
 * @method void setContent(string $content) set content value
 * @method string getContent() get content value
 * @method static \Items[] findByContent(string $content) find objects in database by content
 * @method static \Items findOneByContent(string $content) find object in database by content
 * @method static \Items retrieveByContent(string $content) retrieve object from poll by content, get it from db if not exist in poll
 * @method void setStatus(string $status) set status value
 * @method string getStatus() get status value
 * @method static \Items[] findByStatus(string $status) find objects in database by status
 * @method static \Items findOneByStatus(string $status) find object in database by status
 * @method static \Items retrieveByStatus(string $status) retrieve object from poll by status, get it from db if not exist in poll
 * @method void setAuthor(string $author) set author value
 * @method string getAuthor() get author value
 * @method static \Items[] findByAuthor(string $author) find objects in database by author
 * @method static \Items findOneByAuthor(string $author) find object in database by author
 * @method static \Items retrieveByAuthor(string $author) retrieve object from poll by author, get it from db if not exist in poll
 * @method void setTaxonomy(string $taxonomy) set taxonomy value
 * @method string getTaxonomy() get taxonomy value
 * @method static \Items[] findByTaxonomy(string $taxonomy) find objects in database by taxonomy
 * @method static \Items findOneByTaxonomy(string $taxonomy) find object in database by taxonomy
 * @method static \Items retrieveByTaxonomy(string $taxonomy) retrieve object from poll by taxonomy, get it from db if not exist in poll
 * @method void setLanguage(string $language) set language value
 * @method string getLanguage() get language value
 * @method static \Items[] findByLanguage(string $language) find objects in database by language
 * @method static \Items findOneByLanguage(string $language) find object in database by language
 * @method static \Items retrieveByLanguage(string $language) retrieve object from poll by language, get it from db if not exist in poll
 * @method void setModifiedTime(datetime $modified_time) set modified_time value
 * @method datetime getModifiedTime() get modified_time value
 * @method static \Items[] findByModifiedTime(datetime $modified_time) find objects in database by modified_time
 * @method static \Items findOneByModifiedTime(datetime $modified_time) find object in database by modified_time
 * @method static \Items retrieveByModifiedTime(datetime $modified_time) retrieve object from poll by modified_time, get it from db if not exist in poll
 * @method void setCreatedTime(datetime $created_time) set created_time value
 * @method datetime getCreatedTime() get created_time value
 * @method static \Items[] findByCreatedTime(datetime $created_time) find objects in database by created_time
 * @method static \Items findOneByCreatedTime(datetime $created_time) find object in database by created_time
 * @method static \Items retrieveByCreatedTime(datetime $created_time) retrieve object from poll by created_time, get it from db if not exist in poll
 * @method void setOrdering(integer $ordering) set ordering value
 * @method integer getOrdering() get ordering value
 * @method static \Items[] findByOrdering(integer $ordering) find objects in database by ordering
 * @method static \Items findOneByOrdering(integer $ordering) find object in database by ordering
 * @method static \Items retrieveByOrdering(integer $ordering) retrieve object from poll by ordering, get it from db if not exist in poll
 * @method void setHits(integer $hits) set hits value
 * @method integer getHits() get hits value
 * @method static \Items[] findByHits(integer $hits) find objects in database by hits
 * @method static \Items findOneByHits(integer $hits) find object in database by hits
 * @method static \Items retrieveByHits(integer $hits) retrieve object from poll by hits, get it from db if not exist in poll

 */
abstract class ItemsBase extends ActiveRecord {
    protected static $_tableName = 'items';
    protected static $_phpName = 'Items';
    protected static $_pk = 'id';
    protected static $_alias = 'i';
    protected static $_dbConnectName = 'items';
    protected static $_instances = array();
    protected static $_schema = array(
        'id' => array('name' => 'id',
                'type' => 'integer',
                'primary' => true,
                'auto_increment' => true,
                'db_type' => 'int(11) unsigned',
                'length' => 4),
        'title' => array('name' => 'title',
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'slug' => array('name' => 'slug',
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'excerpt' => array('name' => 'excerpt',
                'type' => 'string',
                'db_type' => 'text'),
        'content' => array('name' => 'content',
                'type' => 'string',
                'db_type' => 'text'),
        'status' => array('name' => 'status',
                'default' => 'PUBLISH',
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'author' => array('name' => 'author',
                'type' => 'string',
                'db_type' => 'varchar(255)',
                'length' => 255),
        'taxonomy' => array('name' => 'taxonomy',
                'default' => 'POST',
                'type' => 'string',
                'db_type' => 'varchar(100)',
                'length' => 100),
        'language' => array('name' => 'language',
                'default' => '*',
                'type' => 'string',
                'db_type' => 'varchar(20)',
                'length' => 20),
        'modified_time' => array('name' => 'modified_time',
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'created_time' => array('name' => 'created_time',
                'type' => 'datetime',
                'db_type' => 'datetime'),
        'ordering' => array('name' => 'ordering',
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
        'hits' => array('name' => 'hits',
                'type' => 'integer',
                'auto_increment' => false,
                'db_type' => 'int(11)',
                'length' => 4),
);
    protected static $_validate = array(
        'title' => array('require' => '"title" is required!'),
        'status' => array('require' => '"status" is required!'),
        'taxonomy' => array('require' => '"taxonomy" is required!'),
        'language' => array('require' => '"language" is required!'),
        'modified_time' => array('require' => '"modified_time" is required!'),
        'created_time' => array('require' => '"created_time" is required!'),
        'ordering' => array('require' => '"ordering" is required!'),
        'hits' => array('require' => '"hits" is required!'),
);
    protected static $_cols = array('id','title','slug','excerpt','content','status','author','taxonomy','language','modified_time','created_time','ordering','hits');

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
            self::addInstanceToPool($this, $this->{$this->getPkValue()});
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
            self::removeInstanceFromPool($this->{$this->getPkValue()});
            return true;
        }
        catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
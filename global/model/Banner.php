<?php 
/**
 * Banner
 *  This class has been auto-generated at 30/06/2013 06:04:52
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/BannerBase.php';
class Banner extends \BannerBase {
    public function validationRules() {
        self::$_validate['file'][] = array(
            'name' => 'Require',
            'message' => "Banner file can not be blank!");

        self::$_validate['title'][] = array(
            'name' => 'Require',
            'message' => 'Banner title can not be blank!',
        );
    }
}
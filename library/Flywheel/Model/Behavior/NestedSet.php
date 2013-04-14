<?php
namespace Flywheel\Model\Behavior;
class NestedSet extends ModelBehavior {
    public $left_attr = 'lft';
    public $right_attr = 'rgt';
    public $level_attr = 'level';

    // storage columns accessors

    public function getLeftValue() {
        return $this->getOwner()->{$this->left_attr};
    }

    /**
     * Proxy setter method for the left value of the nested set model.
     * It provides a generic way to set the value, whatever the actual column name is.
     *
     * @param      int $left The nested set left value
     * @return     \Flywheel\Model\ActiveRecord The current object (for fluent API support)
     */
    public function setLeftValue($left) {
        if (null !== $left) {
            $left = (int) $left;
        }

        $owner = $this->getOwner();
        /* @var \Flywheel\Model\ActiveRecord $owner; */

        if ($left != $owner->{$this->left_attr}) {
            $owner->{$this->left_attr} = $left;
        }

        return $owner;
    }

    /**
     * Get tree right value
     * @return integer
     */
    public function getRightValue() {
        return $this->getOwner()->{$this->right_attr};
    }

    /**
     * @param $right
     * @return \Flywheel\Model\ActiveRecord
     */
    public function setRightValue($right) {
        if (null !== $right) {
            $right = (int) $right;
        }

        $owner = $this->getOwner();
        /* @var \Flywheel\Model\ActiveRecord $owner; */
        if ($right != $owner->{$this->right_attr}) {
            $owner->{$this->right_attr} = $right;
        }

        return $owner;
    }

    public function getLevel() {
        return $this->getOwner()->{$this->level_attr};
    }

    public function setLevel($level) {
        $level = (int) $level;

        $owner = $this->getOwner();
        /* @var \Flywheel\Model\ActiveRecord $owner; */
        if ($level != $owner->{$this->level_attr}) {
            $owner->{$this->left_attr} = $level;
        }

        return $owner;
    }

    // only for behavior with use_scope
    public function getScopeValue() {}

    public function setScopeValue($scope) {}

    // root maker (requires calling save() afterwards)
    public function makeRoot() {}

    // inspection methods
    public function isInTree() {}

    public function isRoot() {}

    public function isLeaf() {}

    public function isDescendantOf() {}

    public function isAncestorOf() {}

    public function hasParent() {}

    public function hasPrevSibling() {}

    public function hasNextSibling() {}

    public function hasChildren() {}

    public function countChildren() {}

    public function countDescendants() {}

    // tree traversal methods

    public function getParent() {}

    public function getPrevSibling() {}

    public function getNextSibling() {}

    public function getChildren() {}

    public function getFirstChild() {}

    public function getLastChild() {}

    public function getSiblings($includeCurrent = false, $query = null) {}

    public function getDescendants($query = null) {}

    public function getBranch($query = null) {}

    public function getAncestors($query = null) {}


    // node insertion methods (require calling save() afterwards)
    public function addChild($node) {}

    public function insertAsFirstChildOf($node) {}

    public function insertAsLastChildOf($node) {}

    public function insertAsPrevSiblingOf($node) {}

    public function insertAsNextSiblingOf($node) {}

    // node move methods (immediate, no need to save() afterwards)
    public function moveToFirstChildOf($node) {}

    public function moveToLastChildOf($node) {}

    public function moveToPrevSiblingOf($node) {}

    public function moveToNextSiblingOf($node) {}

    // deletion methods
    public function deleteDescendants() {}

    // only for behavior with method_proxies
    public function createRoot() {}

    public function retrieveParent() {}

    public function retrievePrevSibling() {}

    public function retrieveNextSibling() {}

    public function retrieveFirstChild() {}

    public function retrieveLastChild() {}

    public function getPath() {}
}

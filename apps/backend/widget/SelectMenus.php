<?php
class SelectMenus extends AdminBaseWidget {
    public function begin() {
        $this->lists = Menus::retrieveRoot()->getDescendants();

        if (!$this->label) {
            $this->label = t('Select Parent');
        }
    }

    public function end() {
        $select = $this->form->selectOption($this->elementName, $this->selected, (array) $this->htmlOptions)
            ->addOption($this->label, '0');

        foreach ($this->lists as $item) {
            $selectName = ($item->getLevel() > 1)? str_repeat('&#8212;', $item->getLevel()-1) .$item->getName(): $item->getName();
            $select->addOption($selectName, $item->getId());
        }

        ob_start();
        $select->display();
        $s = ob_get_clean();
        return $s;
    }
}
<?php

class Magestore_Membership_Model_Mysql4_Membership extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the membership_id refers to the key field in your database table.
        $this->_init('membership/membership', 'membership_id');
    }
}
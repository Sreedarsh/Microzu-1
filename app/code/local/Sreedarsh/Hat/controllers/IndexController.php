<?php

/**
 * Hire A Technician
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Hat
 * @author Sreedarsh <sreedarsh88@gmail.com>
 */
class Sreedarsh_Hat_IndexController extends Mage_Core_Controller_Front_Action {
    
    
    public function indexAction() {
        $this->loadLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('sreedarsh_hat')->__('Hire A Technician'));
        }
        
        $this->renderLayout();
    }
    
    public function categoryAction() {
       echo Mage::app()->getRequest()->getParam('catid');
       
       $customer = Mage::getSingleton('customer/session')->getCustomer();
       
        $model = Mage::getModel('sreedarsh_hat/tech');
        $cus_id = $customer->getId();
        $customer_model = Mage::getModel('customer/customer')->load($cus_id);
        $technician = $model->load($cus_id,'customer_id');
    
        
        if($technician->getId()){
            $update_data = array('child_id' => Mage::app()->getRequest()->getParam('catid'));
        $save = $model->load($cus_id,'customer_id')->addData($update_data); 
        $customer_model->setTechnicianCategory(Mage::app()->getRequest()->getParam('catid'));
        $customer_model->save();
        $save->save();
        }
        else{
        $data = array('child_id' => Mage::app()->getRequest()->getParam('catid'), 'customer_id' => $customer->getId());
        $save = $model->setData($data);
        $customer_model->setTechnicianCategory(Mage::app()->getRequest()->getParam('catid'));
        $customer_model->save();
        $save->save();
       
       
       
    }
   
    
}
}

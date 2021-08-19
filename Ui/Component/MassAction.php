<?php

namespace IWD\OrderManager\Ui\Component;

/**
 * Class PdfAction
 */
class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $request = $objectManager->get('Magento\Framework\App\Request\Http');
        $order = $scopeConfig->getValue('iwdordermanager/allow_delete/orders');

        parent::prepare();
        $config = $this->getConfiguration();

        $allowedActions = [];

        foreach ($config['actions'] as $action) {
            if($request->getControllerName() == 'order' && $action['type'] == 'delete' && $order){
                $allowedActions[] = $action;
            }elseif($action['type'] != 'delete'){
                if($action['type'] == 'status'){
                    array_unshift($allowedActions,$action);
                }else{
                    $allowedActions[] = $action;
                }
            }
        }

        $config['actions'] = $allowedActions;

        $this->setData('config', (array)$config);
    }
}

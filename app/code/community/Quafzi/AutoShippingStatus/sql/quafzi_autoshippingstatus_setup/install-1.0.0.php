<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    UPDATE {$this->getTable('core/config_data')}
        SET path='shipping/option/statusBeforeSwitchToShipped'
        WHERE path='shipping/option/partialShippingStatuses';

    UPDATE {$this->getTable('core/config_data')}
        SET path='shipping/option/statusAfterSwitchToShipped'
        WHERE path='shipping/option/shippedStatus';
");

$installer->endSetup();

<?php

namespace Productflow\Adapter\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	/**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }


	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('prductflow_cron_schedule')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('prductflow_cron_schedule')
			)
				->addColumn(
					'schedule_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Schedule ID'
				)
				->addColumn(
					'job_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					'Job code'
				)
				->addColumn(
					'payload',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'2M',
					['nullable' => true],
					'Payload json for Creating/Updating'
				)
                ->addColumn(
					'messages',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'2M',
					['nullable' => true],
					'Payload json for Creating/Updating'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					1,
					[],
					'Status (processing,pending,completed,error)'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Started At'
				)
                ->addColumn(
					'scheduled_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					'Scheduled At')
                ->addColumn(
                    'executed_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Executed At') 
                ->addColumn(
                    'finished_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Finished At')       
				->setComment('Productflow Api Job Queue');
			$installer->getConnection()->createTable($table);

		}

		if (!$installer->tableExists('prductflow_order_shipment_queue')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('prductflow_order_shipment_queue')
			)
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Queue ID'
				)
				->addColumn(
					'order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					'order Id'
				)
				->addColumn(
					'sub_order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					'Sub order Id'
				)
				->addColumn(
					'payload',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'2M',
					['nullable' => true],
					'Payload json for Creating/Updating'
				)
                ->addColumn(
					'messages',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'2M',
					['nullable' => true],
					'Payload json for Creating/Updating'
				)    
				->setComment('Productflow Api Sub Orders');
			$installer->getConnection()->createTable($table);

		}

		$installer->getConnection()->addColumn(
			$installer->getTable('catalog_eav_attribute'),
			'include_in_datamodel',
			[
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				'nullable' => true,
				'length' => '12,4',
				'comment' => 'Include In Datamodel',
				'default' => '1',
				'after' => 'is_filterable_in_grid',
			]
		);

		
		$installer->endSetup();
	}
}
<?php
declare(strict_types=1);

namespace Sistche\ChangeButtonColor\Console\Command;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeColor extends Command
{
    private const COLOR = 'hex';
    private const STORE = 'store';
    private const XML_PATH_COLOR = 'design/button/color';

    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected WriterInterface $configWriter,
        protected CacheManager $cacheManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('color:change')
            ->setDescription('Change buttons color by store-view')
            ->addArgument(self::COLOR, InputArgument::REQUIRED, ('HEX color'))
            ->addArgument(self::STORE, InputArgument::REQUIRED, ('Store View ID'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hex = $input->getArgument(self::COLOR);
        $storeId = (int) $input->getArgument(self::STORE);

        if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            $output->writeln('<error>' . __('Invalid HEX color') . '</error>');
            return Command::FAILURE;
        }

        try {
            $this->storeManager->getStore($storeId);
        } catch (\Exception $e) {
            $output->writeln('<error>' . __('Store view does not exist') . '</error>');
            return Command::FAILURE;
        }

        $this->configWriter->save(
            self::XML_PATH_COLOR,
            '#' . $hex,
            'stores',
            $storeId
        );

        $output->writeln('<info>' . __('Button color updated successfully') . '</info>');

        $this->cacheManager->clean([
            'config',
            'layout',
            'full_page'
        ]);

        $output->writeln('<comment>' . __('Cache cleaned automatically') . '</comment>');

        return Command::SUCCESS;
    }
}
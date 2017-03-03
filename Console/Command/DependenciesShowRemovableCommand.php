<?php
namespace AvS\DisableModules\Console\Command;

use AvS\DisableModules\Console\Report\RemovableModules\Builder;
use AvS\DisableModules\Console\Report\RemovableModules\Parser;
use AvS\DisableModules\Console\Report\RemovableModules\Writer;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Setup\Module\Dependency\Report\Dependency;

/**
 * Command for showing nmodules which can be removed / disabled
 */
class DependenciesShowRemovableCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * Input key for directory option
     */
    const INPUT_KEY_DIRECTORY = 'directory';

    /**
     * Input key for output path of report file
     */
    const INPUT_KEY_OUTPUT = 'output';

    /**
     * @var ComponentRegistrarInterface
     */
    private $registrar;
    /**
     * @var DirSearch
     */
    private $dirSearch;
    /**
     * @var ThemePackageList
     */
    private $themePackageList;
    /**
     * @var Writer
     */
    private $writer;
    /**
     * @var Parser
     */
    private $parser;

    /**
     * DependenciesShowRemovableCommand constructor.
     * @param ComponentRegistrarInterface $registrar
     * @param DirSearch $dirSearch
     * @param ThemePackageList $themePackageList
     * @param Writer $writer
     * @param Parser $parser
     */
    public function __construct(
        ComponentRegistrarInterface $registrar,
        DirSearch $dirSearch,
        ThemePackageList $themePackageList,
        Writer $writer,
        Parser $parser
    ) {
        $this->registrar = $registrar;
        $this->dirSearch = $dirSearch;
        $this->themePackageList = $themePackageList;
        $this->writer = $writer;
        parent::__construct();
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Shows modules which can be removed / disabled')
            ->setName('info:dependencies:show-removable');
        $this->setDefinition(
            [
                new InputOption(
                    self::INPUT_KEY_OUTPUT,
                    'o',
                    InputOption::VALUE_REQUIRED,
                    'Report filename',
                    $this->getDefaultOutputFilename()
                )
            ]
        );
        parent::configure();
    }

    /**
     * Return default output filename for framework dependencies report
     *
     * @return string
     */
    private function getDefaultOutputFilename()
    {
        return 'modules-removable.csv';
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            Files::setInstance(new Files($this->registrar, $this->dirSearch, $this->themePackageList));
            $this->buildReport($input->getOption(self::INPUT_KEY_OUTPUT));
            $output->writeln('<info>Report successfully processed. File "' . $input->getOption(self::INPUT_KEY_OUTPUT) . '" generated.</info>');
        } catch (\Exception $e) {
            $output->writeln(
                '<error>Please check the path you provided. Removable Modules report generator failed with error: ' .
                $e->getMessage() . '</error>'
            );
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * Build Framework dependencies report
     *
     * @param string $outputPath
     * @return void
     */
    protected function buildReport($outputPath)
    {
        $filesForParse = Files::init()->getComposerFiles(ComponentRegistrar::MODULE, false);

        $this->getBuilder()->build(
            [
                'parse' => ['files_for_parse' => $filesForParse],
                'write' => ['report_filename' => $outputPath],
            ]
        );
    }

    protected function getBuilder()
    {
        return new Builder(
            $this->parser,
            $this->writer
        );
    }
}

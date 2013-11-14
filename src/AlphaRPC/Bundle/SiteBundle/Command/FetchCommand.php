<?php
namespace AlphaRPC\Bundle\SiteBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DirectoryIterator;

class FetchCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('alpharpc:fetch')
            ->setDescription('Create links for alpharpc docs..')
            ->setDefinition(array(
            ))
            ->setHelp(<<<EOT
The <info>create-links</info> command, searches for docs and adds them to the site.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = new DirectoryIterator('vendor/alphacomm/alpharpc/doc');
        $targetDir = 'source';
        $output->writeln(array(
            'Reading:',
            $files->getPath(),
            '',
        ));
        foreach ($files as $file) {
            if ($file->getExtension() != 'md') {
                continue;
            }
            $name = $file->getFilename();
            $targetFile = $targetDir.'/'.$name;
            $output->write(self::pad($name, 25).' => '.self::pad($targetFile, 35).' ');
            if (file_exists($targetFile)) {
                $output->writeln('[<comment>Exists!</comment>]');
            } elseif (@copy($file->getRealPath(), $targetFile)) {
                $output->writeln('[<info>OK</info>]');
            } else {
                $output->writeln('[<error>FAIL</error>]');
            }
            $this->convert($targetFile);
        }
    }

    private function convert($file)
    {
        $content = file_get_contents($file);
        if (substr($content, 0, 3) !== '---') {
            $content = '---'.PHP_EOL.'layout: default'.PHP_EOL.'---'.PHP_EOL.PHP_EOL.$content;
            file_put_contents($file, $content);
        }
    }

    private static function pad($string, $length)
    {
        return str_pad($string, $length, ' ', STR_PAD_RIGHT);
    }
}

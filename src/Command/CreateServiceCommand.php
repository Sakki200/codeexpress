<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'create:service',
    description: 'Create a new service file',
)]
class CreateServiceCommand extends Command
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $serviceName = $input->getArgument('name');
        $filesystem = new Filesystem();

        // Création du dossier Service s'il n'existe pas
        $serviceDir = $this->projectDir . '/src/Service';
        if (!$filesystem->exists($serviceDir)) {
            $filesystem->mkdir($serviceDir);
        }

        // Vérification et création de AbstractService si nécessaire
        $abstractServicePath = $serviceDir . '/AbstractService.php';
        if (!$filesystem->exists($abstractServicePath)) {
            $this->createAbstractService($abstractServicePath);
            $io->success('AbstractService created successfully.');
        }

        // Création du nouveau service
        $serviceFilePath = $serviceDir . '/' . $serviceName . '.php';
        $this->createService($serviceFilePath, $serviceName);

        $io->success("Service $serviceName created successfully.");

        return Command::SUCCESS;
    }

    private function createAbstractService(string $path): void
    {
        $content = <<<EOT
<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractService
{
    public function __construct(
        protected ParameterBagInterface \$parameterBag,
        protected MailerInterface \$mailer
    ) {}
}
EOT;

        file_put_contents($path, $content);
    }

    private function createService(string $path, string $serviceName): void
    {
        $content = <<<EOT
<?php

namespace App\Service;

class $serviceName extends AbstractService
{
    // Add your service methods here
}
EOT;

        file_put_contents($path, $content);
    }
}

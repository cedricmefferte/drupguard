<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class DrupGuardInstall extends Command
{
    protected static $defaultName = 'drupguard:install';

    protected $commands = [
      [
        'name' => 'doctrine:schema:drop',
        'parameters' => [
          '--force' => true,
        ],
        'message' => 'Ensure empty database',
        'successMessage' => 'Database schema dropped successfully.',
        'errorMessage' => 'Database schema drop fail.'
      ],
      [
        'name' => 'doctrine:schema:create',
        'parameters' => [
          '--no-interaction' => true,
        ],
        'message' => 'Create schema',
        'successMessage' => 'Database schema created successfully.',
        'errorMessage' => 'Database schema creation fail.'
      ]
    ];

    private $projectDir;
    protected $entityManager;
    protected $passwordEncoder;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->projectDir = $kernel->getProjectDir();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this
          ->setDescription('Install Drupguard.')
          ->setHelp('This command install Drupguard.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->title('Installing DrupGuard');
        $outputStyle->writeln($this->getDrupGuardLogo());

        //check .env.local
        $filesystem = new Filesystem();

        $nbStep = count($this->commands)+1;
        $commandStart = 1;
        $noOutput = new NullOutput();
        if(!$filesystem->exists($this->projectDir . '/.env.local')) {
            $nbStep++;
            $commandStart++;

            $outputStyle->section(
              sprintf(
                'Step %d of %d. <info>%s</info>',
                1,
                $nbStep,
                'Create .env.local file'
              )
            );
            try {
                $databaseQuestion = new Question(
                  'Database url (mysql://DB_USER:DB_PASSWORD@DB_HOST:DB_PORT/DB_NAME?serverVersion=DB_SERVER_VERSION)'
                );
                $databaseQuestion->setValidator(
                  function ($answer) {
                      if (!is_string($answer) || !preg_match(
                          '#^mysql://[^:]+:[^@]+@[^:]+:[1-9]\d+/[^\?]+\?serverVersion=.*$#i',
                          $answer
                        )) {
                          throw new \RuntimeException(
                            'The database url\'s format should be : mysql://DB_USER:DB_PASSWORD@DB_HOST:DB_PORT/DB_NAME?serverVersion=DB_SERVER_VERSION'
                          );
                      }

                      return $answer;
                  }
                );
                $databaseUrl = $outputStyle->askQuestion($databaseQuestion);

                $mailerQuestion = new Question(
                  'Mailer DSN (smtp://MAILER_HOST:MAILER_PORT)'
                );
                $mailerQuestion->setValidator(
                  function ($answer) {
                      if (!is_string($answer) || ($answer !== 'sendmail://default' && $answer !== 'native://default' && !preg_match(
                          '#^smtp://[^:]+:[1-9]\d+$#i',
                          $answer
                        ))) {
                          throw new \RuntimeException(
                            'The mailer dsn\'s format should be : smtp://MAILER_HOST:MAILER_PORT or sendmail://default or native://default'
                          );
                      }

                      return $answer;
                  }
                );
                $mailerDsn = $outputStyle->askQuestion($mailerQuestion);

                $envLocal = <<<EOT
DATABASE_URL={$databaseUrl}
MAILER_DSN={$mailerDsn}

EOT;
                file_put_contents($this->projectDir.'/.env.local', $envLocal);
                $outputStyle->success('File .env.local created.');
            }
            catch(\Exception $e) {
                $outputStyle->error('File .env.local creation failed.');
                return Command::FAILURE;
            }

            $_ENV['DATABASE_URL'] = $databaseUrl;
            $_ENV['MAILER_DSN'] = $mailerDsn;

            $commandObj = $this->getApplication()->find('cache:clear');
            $commandParameters = new ArrayInput([]);
            if($commandObj->run($commandParameters, $noOutput) != Command::SUCCESS) {
                $outputStyle->error('Cache clear failed.');
                return Command::FAILURE;
            }
            else {
                $outputStyle->success('Cache has been successfully cleared.');
            }
        }

        $dropQuestion = new ConfirmationQuestion('Existing database will be dropped, continue ?', false);
        if (!$outputStyle->askQuestion($dropQuestion)) {
            $outputStyle->warning('Installation aborted.');
            return Command::SUCCESS;
        }

        foreach ($this->commands as $step => $command) {
            $outputStyle->section(
              sprintf(
                'Step %d of %d. <info>%s</info>',
                $step + $commandStart,
                $nbStep,
                $command['message']
              )
            );

            $commandObj = $this->getApplication()->find(
              $command['name']
            );
            $commandParameters = new ArrayInput($command['parameters']);
            if($commandObj->run($commandParameters, $noOutput) != Command::SUCCESS) {
                $outputStyle->error($command['errorMessage']);
                return Command::FAILURE;
            }
            else {
                $outputStyle->success($command['successMessage']);
            }
        }

        $outputStyle->section(
          sprintf(
            'Step %d of %d. <info>%s</info>',
            $nbStep,
            $nbStep,
            'Create super admin user'
          )
        );
        try {
            $user = new User();
            $user
              ->setUsername('admin')
              ->setFirstname('admin')
              ->setLastname('admin')
              ->setIsVerified(true)
              ->setEmail('admin@drupguard.com')
              ->setPassword(
                $this->passwordEncoder->encodePassword(
                  $user,
                  'admin'
                )
              )
              ->setRoles(['ROLE_SUPER_ADMIN']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $outputStyle->success('Admin user created.');
        }
        catch (\Exception $e) {
            $outputStyle->error('Admin user creation failed.');
            return Command::FAILURE;
        }

        $outputStyle->section('End');
        $outputStyle->comment('Please connect as Admin user with credentials username/password: . <info>admin / admin</info>');
        $outputStyle->success('Drupguard has been successfully installed.');

        return Command::SUCCESS;
    }

    private function getDrupguardLogo(): string
    {
        return '
                           .@,                               
                           @@***,                            
                         /@@%*****,                          
                     ,@@@%%/***********,                     
                ,*@@@@@@@%%*****************,                
            ./@@@@@@@@@%%***********************,            
          %@@@@@@@@@%%%****************************,         
       ,@@@@@@@@%%%#*********************************,       
     ,&@@@@@%%%%**************************************,,                    
    *%%%%%%*******************************************,,,                   
   **************************************************,,,,,                  
  ***************************************************,,,,,,         8888888b.                                                                   888 
 ,**************************************************,,,,,,,,        888  "Y88b                                                                  888 
 *************************************************,,,,,,,,,,        888    888                                                                  888 
,***********************************************,,,,,,,,,,,,        888    888 888d888 888  888 88888b.   .d88b.  888  888  8888b.  888d888 .d88888 
,*******************#@@@@@@@&****************,,,,,,,,,,,,,,,        888    888 888P"   888  888 888 "88b d88P"88b 888  888     "88b 888P"  d88" 888 
 ***************/@@@@@@@@@@@@@@@@#********,,,,,,,#@@@@@@@@,,        888    888 888     888  888 888  888 888  888 888  888 .d888888 888    888  888 
 ,*************@@@@@@@@@@@@@@@@@@@@@@**,,,,,,,@@@@@@@@@@@@,,        888  .d88P 888     Y88b 888 888 d88P Y88b 888 Y88b 888 888  888 888    Y88b 888 
  ************@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,        8888888P"  888      "Y88888 88888P"   "Y88888  "Y88888 "Y888888 888     "Y88888 
  .**********%@@@@@@@@@@@@@@@@@@@@@@@@@,,,,(@@@@@@@@@@@@@@,                                     888           888                                  
    **********@@@@@@@@@@@@@@@@@@@@,,,,,,,,,,,,@@@@@@@@@@@,                                      888      Y8b d88P                                  
     ,,********%@@@@@@@@@@@@@@,,,,,,@@@@@@@@,,,,,@@@@@@,                                        888       "Y88P"                                   
       ,,,,,,,,,,,,,,,,,,,,,,,,,,*@%,,,,,,,@@,,,,,,,,,,                   
         ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,         
            ,,,,,,,,,,,,,,,@@@@,,,,,,,,,,,@@@@,,,.           
                ,,,,,,,,,,,,,,,%@@@@@@@@*,,,,.               
                      ,,,,,,,,,,,,,,,,,,                     '
          ;
    }
}
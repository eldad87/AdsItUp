<?php
namespace AppBundle\Command;

use AppBundle\UserBundle\Doctrine\UserManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Command\CreateUserCommand AS BasicCreateUserCommand;


class CreateUserCommand extends BasicCreateUserCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $help = $this->getHelp();
        $help = str_ireplace('matthieu@example.com mypassword', 'matthieu@example.com mypassword brandId', $help);

        $this->setName('app:user:create')
            ->setHelp($help)
            ->setDefinition(array(
				new InputArgument('username', InputArgument::REQUIRED, 'The username'),
				new InputArgument('email', InputArgument::REQUIRED, 'The email'),
				new InputArgument('password', InputArgument::REQUIRED, 'The password'),
				new InputArgument('brandId', InputArgument::REQUIRED, 'The brand'),
				new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
				new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
			));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $brandId    = $input->getArgument('brandId');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        /** @var UserManager $userManager */
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $brand = $userManager->getEntityManager()->getReference('AppBundle\Entity\Brand', $brandId);
        $userManager->setBrand($brand);

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->create($username, $password, $email, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        if (!$input->getArgument('brandId')) {
            $brandId = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a brand id:',
                function($brandId) {
                    if (empty($brandId)) {
                        throw new \Exception('brandId can not be empty');
                    }

                    return $brandId;
                }
            );
            $input->setArgument('brandId', $brandId);
        }
    }
}

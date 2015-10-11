<?php
namespace AppBundle\Command;

use AppBundle\UserBundle\Doctrine\UserManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Command\ChangePasswordCommand AS BasicChangePasswordCommand;


class ChangePasswordCommand extends BasicChangePasswordCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $help = $this->getHelp();
        $help = str_ireplace('mmatthieu mypassword', 'matthieu mypasswordbrandId', $help);

        $this->setName('app:user:change-password')
            ->setHelp($help)
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
				new InputArgument('brandId', InputArgument::REQUIRED, 'The brand'),
			));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $brandId    = $input->getArgument('brandId');

        /** @var UserManager $userManager */
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $brand = $userManager->getEntityManager()->getReference('AppBundle\Entity\Brand', $brandId);
        $userManager->setBrand($brand);

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->changePassword($username, $password);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));
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

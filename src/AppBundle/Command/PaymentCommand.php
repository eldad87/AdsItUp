<?php

namespace AppBundle\Command;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\CommissionPlan;
use AppBundle\Entity\PaymentLog;
use AppBundle\Entity\PixelLog;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PaymentCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:payment:sync')
            ->setDescription('Fire pending Payments.')
            ->setDefinition(array(
                new InputArgument('maxRecords', InputArgument::OPTIONAL, 'Maximum payment-records to process', 1000)
            ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Payment Sync start');
        $maxRecords = $input->getArgument('maxRecords');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        do {
            //Get pending pixels
            $qb = $em->createQueryBuilder()
                ->select('pl', 'u')
                ->from('AppBundle:PaymentLog', 'pl')
                ->leftJoin('pl.user', 'u')
                ->where('pl.isProcessed = :isProcessed')
                ->setParameter('isProcessed', false)
                ->orderBy('pl.updatedAt')
                ->getQuery()
                ->setMaxResults(100);
            $commissionPending = $qb->getResult();
            if($commissionPending) {
                $output->writeln(sprintf('Processing %d records', $commissionPending));
            }

            $this->process($commissionPending);

            $maxRecords -= count($commissionPending);
        } while($commissionPending && $maxRecords>0);
        $output->writeln('Done');
    }

    private function process(array $records)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        /** @var PaymentLog $record */
        foreach($records as $record) {

            $qb = $em->createQueryBuilder();
            $qb
                ->update('AppBundle:User', 'u')
                ->set('u.balance', $qb->expr()->diff('u.balance', $record->getAmount()))
                ->where('u.id = :user')
                ->setParameter('user', $record->getUser())
                ->getQuery()
                ->execute();

            $record->setIsProcessed(true);
            $em->persist($record);
        }

        $em->flush();
    }
}

<?php

namespace AppBundle\Command;

use AppBundle\Entity\BrandRecord;
use AppBundle\Entity\CommissionPlan;
use AppBundle\Entity\PixelLog;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PayoutCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:payout:sync')
            ->setDescription('Fire pending Payouts.')
            ->setDefinition(array(
                new InputArgument('maxRecords', InputArgument::OPTIONAL, 'Maximum payout-records to process', 1000)
            ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Payout Sync start');
        $maxRecords = $input->getArgument('maxRecords');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        do {
            //Get pending pixels
            $qb = $em->createQueryBuilder()
                ->select('br', 'u', 'r', 'cp')
                ->from('AppBundle:BrandRecord', 'br')
                ->leftJoin('br.commissionPlan', 'cp')
                ->leftJoin('br.user', 'u')
                ->leftJoin('br.referrer', 'r')
                ->where('br.isProcessed = :isProcessed')
                ->setParameter('isProcessed', false)
                ->orderBy('br.updatedAt')
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

        /** @var BrandRecord $record */
        foreach($records as $record) {
            /** @var CommissionPlan $cp */
            $cp = $record->getCommissionPlan();

            if($cp) {
                if($cp->getPayout()) {
                    $qb = $em->createQueryBuilder();
                    $qb
                        ->update('AppBundle:User', 'u')
                        ->set('u.balance', $qb->expr()->sum('u.balance', $cp->getPayout()))
                        ->where('u.id = :user')
                        ->setParameter('user', $record->getUser())
                        ->getQuery()
                        ->execute();

                    $record->setPayout($cp->getPayout());
                    $em->persist($record->getUser());
                }

                if($record->getReferrer() && $cp->getReferrerPayout()) {
                    $qb = $em->createQueryBuilder();
                    $qb
                        ->update('AppBundle:User', 'u')
                        ->set('u.balance', $qb->expr()->sum('u.balance', $cp->getReferrerPayout()))
                        ->where('u.id = :user')
                        ->setParameter('user', $record->getReferrer())
                        ->getQuery()
                        ->execute();

                    $record->setReferrerPayout($cp->getReferrerPayout());
                    $em->persist($record->getReferrer());
                }
            }

            $record->setIsProcessed(true);
            $em->persist($record);
        }

        $em->flush();
    }
}

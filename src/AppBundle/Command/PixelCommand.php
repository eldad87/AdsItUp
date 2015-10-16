<?php

namespace AppBundle\Command;

use AppBundle\Entity\PixelLog;
use AppBundle\Services\Platform\Pixel\PixelSetting;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PixelCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:pixel:fire')
            ->setDescription('Fire pending Pixels.')
            ->setDefinition(array(
                new InputArgument('maxPixels', InputArgument::OPTIONAL, 'Maximum pixels to process', 1000),
                new InputArgument('maxParallel', InputArgument::OPTIONAL, 'Maximum pixels to fire parallel', 4),
                new InputArgument('timeout', InputArgument::OPTIONAL, 'Timeout for pixel', 30)
            ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Pixel Sync start');
        $maxPixels = $input->getArgument('maxPixels');
        $maxParallel = $input->getArgument('maxParallel');
        $timeout = $input->getArgument('timeout');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        do {
            //Get pending pixels
            $qb = $em->createQueryBuilder()
                ->select('pl')
                ->from('AppBundle:PixelLog', 'pl')
                ->where('pl.status IN(:status)')
                ->andWhere('pl.nextAttempt <= :now')
                ->andWhere('pl.attempts < 5')
                ->setParameter('status', array(PixelLog::STATUS_SERVER_PENDING, PixelLog::STATUS_ERROR))
                ->setParameter('now', new \DateTime())
                ->getQuery()
                ->setMaxResults(100);
            $pendingPixels = $qb->getResult();

            if($pendingPixels) {
                $output->writeln(sprintf('Processing %d records', $pendingPixels));
            }

            $this->fire($pendingPixels, $maxParallel, $timeout);
            $em->flush();

            $maxPixels -= count($pendingPixels);
        } while($pendingPixels && $maxPixels>0);
        $output->writeln('Done');
    }

    protected function fire(array $pixels, $maxParallel, $timeout)
    {
        $curlMaster = curl_multi_init();

        $maxParallel = min($maxParallel, count($pixels));
        /** @var PixelLog $pixel */
        for($i=0; $i<$maxParallel; $i++) {
            $ch = $this->getPixelCURLOptions($pixels[$i], $i, $timeout);
            curl_multi_add_handle($curlMaster, $ch);
        }

        do {
            while(($execrun = curl_multi_exec($curlMaster, $running)) == CURLM_CALL_MULTI_PERFORM);
            if($execrun != CURLM_OK) {
                break;
            }
            while ($done = curl_multi_info_read($curlMaster)) {
                //Handle response
                $key = curl_getinfo($done['handle'], CURLINFO_PRIVATE);
                $httpCode = curl_getinfo($done['handle'], CURLINFO_HTTP_CODE);
                $body = curl_multi_getcontent($done['handle']);
                $this->handlePixelResult($pixels[$key], $httpCode, $body);

                //Add a new request
                $i++;
                if(count($pixels)>$i) {
                    $ch = $this->getPixelCURLOptions($pixels[$i], $i, $timeout);
                    curl_multi_add_handle($curlMaster, $ch);
                }

                //Remove current-ended request
                curl_multi_remove_handle($curlMaster, $done['handle']);
            }
        } while ($running);

        curl_multi_close($curlMaster);
        return true;
    }

    protected function handlePixelResult(PixelLog $pixelLog, $httpCode, $body)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $pixelLog->setAttempts($pixelLog->getAttempts()+1);
        if($httpCode >= 200 && $httpCode < 300) {
            $pixelLog->setStatus(PixelLog::STATUS_SUCCESS);
        } else {
            $pixelLog->setStatus(PixelLog::STATUS_ERROR);
            $pixelLog->setNextAttempt(new \DateTime('+1 Day'));
        }

        $pixelLog->setResponseCode($httpCode);
        $pixelLog->setResponseBody($body);

        $em->persist($pixelLog);
    }

    private function getPixelCURLOptions(PixelLog $pixelLog, $pos, $timeout)
    {
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_URL => $pixelLog->getUrl(),
            CURLOPT_PRIVATE => $pos,
        );
        if(PixelSetting::ACTION_POST == $pixelLog->getAction()) {
            $curlOptions[CURLOPT_POST] = true;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        return $ch;
    }
}

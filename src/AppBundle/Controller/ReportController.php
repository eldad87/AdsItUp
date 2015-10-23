<?php

namespace AppBundle\Controller;

use AppBundle\Services\Report\Report;
use APY\DataGridBundle\Grid\Column\BlankColumn;
use APY\DataGridBundle\Grid\Column\Column;
use APY\DataGridBundle\Grid\Grid;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 *
 * @Breadcrumb("Dashboard", route={"name"="dashboard"})
 */
class ReportController extends AbstractController
{
    /**
     * Report
     * @Breadcrumb("{entity}")
     * @Route("/Dashboard/Report/{entity}", name="dashboard.report",  requirements={"entity":"BrandRecord|PixelLog"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_AFFILIATE')")
     */
    public function reportAction(Request $request, $entity)
    {
        $source = new Entity('AppBundle:' . $entity);

        //Get all fields from entity
        /** @var Grid $grid */
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setNoDataMessage(false);
        $column = new BlankColumn(array('id'=>'total', 'title'=>'total', 'isManualField'=>true, 'field'=>'total'));
        $grid->addColumn($column, 1);

        //Set permissions
        $qb = $source->getRepository()->createQueryBuilder($source->getTableAlias());
        $this->applyPermission($qb);
        $source->initQueryBuilder($qb);

        //Generate a list of fields to break down by
        $columns = array();
        /** @var Column $column */
        foreach($grid->getColumns() AS $column) {
            if(!$column->isVisible()) {
                continue;
            }
            $columns[$column->getField()] = $column->getTitle();
        }

        //Get the form which the user will use to chose the breakdown
        /** @var Session $session */
        $session = $this->get('session');
        /** @var FormBuilder $form */
        $form = $this->container->get('form.factory')->createBuilder(
            'form',
            $session->get('report_' . md5($source->getEntityName()), array('fields'=>array_keys($columns))),
            array('csrf_protection' => false)
        )
            ->add('fields', 'choice', array(
                'choices' => $columns,
                'multiple' => true,
                'required' => false
            ))
            ->add('save', 'submit', array('label' => 'Generate'))
            ->getForm();

        //Get selected breakdown fields
        /** @var Form $form */
        $form->handleRequest($request);
        $aggrigationFields = array_intersect_key($form->getData(), array('fields'=>1));
        $session->set('report_' . md5($source->getEntityName()), $aggrigationFields);

        //Apply breakdown
        $source->manipulateQuery(
            function (QueryBuilder $query) use ($grid, $aggrigationFields)
            {
                $query->resetDQLPart('groupBy');
                $query->addSelect(sprintf('count(DISTINCT %s.%s) total', $query->getRootAliases()[0], $grid->getColumns()->getPrimaryColumn()->getField()));

                /** @var Column $column */
                foreach($grid->getColumns() AS $column) {
                    if(!$column->isVisible() || 'total' == $column->getField()) {
                        continue;
                    }

                    if(!in_array($column->getField(), $aggrigationFields['fields'])) {
                        $column->setVisible(false);
                        continue;
                    }

                    if(false === strpos($column->getField(), '.')) {
                        $pattern = sprintf('%s.%s', $query->getRootAliases()[0], $column->getField());
                    } else {
                        $pattern = sprintf('_%s', $column->getField()); //Dirty hack, because $query->getRootAliases() is not working as expected
                    }

                    //In case and the user cast date fields (datetime->date) we need to HACK IT in order to group by it
                    if(false !== strpos($pattern, ':')) {
                        $func = substr($pattern, strpos($pattern, ':') +1);
                        $pattern = substr($pattern, 0, ((-1*strlen($func))-1));

                        $alias = str_replace(array(':', '.'), array('',''), $pattern) . 'HiddenDate';
                        switch($func){
                            case 'date':
                                $query->addSelect(sprintf('DATE(%s) AS HIDDEN %s ', $pattern, $alias));
                                $pattern = $alias;
                                break;
                            case 'month':
                                $query->addSelect(sprintf('MONTH(%s) AS HIDDEN %s ', $pattern, $alias));
                                $pattern = $alias;
                                break;
                            case 'day':
                                $query->addSelect(sprintf('DAY(%s) AS HIDDEN %s ', $pattern, $alias));
                                $pattern = $alias;
                                break;
                            case 'year':
                                $query->addSelect(sprintf('YEAR(%s) AS HIDDEN %s ', $pattern, $alias));
                                $pattern = $alias;
                                break;
                        }
                    }

                    $query->addGroupBy($pattern);
                }
            }
        );

        $grid->isReadyForRedirect();

        return $grid->getGridResponse('::listGrid.html.twig', array('gridParams'=>array('form'=>$form->createView())));
    }
}
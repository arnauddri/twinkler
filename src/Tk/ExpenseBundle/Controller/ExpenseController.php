<?php

namespace Tk\ExpenseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\ExpenseBundle\Entity\Expense;
use Tk\ExpenseBundle\Form\ExpenseType;

class ExpenseController extends Controller
{
    public function indexAction()
    {
        return $this->render('TkExpenseBundle::index.html.twig', array(
            'all_expenses'        => $this->getAllExpensesAction(),
            'my_expenses'         => $this->getMyExpensesAction(),
            'other_expenses'      => $this->getOtherExpensesAction(),
            'total_paid'          => $this->getTotalPaidAction(),
            'total_paid_by_me'    => $this->getTotalPaidByMeAction(),
            'total_paid_supposed' => $this->getTotalSupposedPaidAction(),
            'total_paid_for_me'   => $this->getTotalPaidForMeAction(),
            'debts'               => $this->getCurrentDebtsAction(),
            ));
    }

    public function newAction()
    {
    	$expense = new Expense();
    	$expense->setAddedDate(new \DateTime('now'));
        $expense->setDate(new \Datetime('today'));
    	$expense->setActive(true);

        $member = $this->getUser()->getCurrentMember();
        $expense->setAuthor($member);
        $expense->setGroup($member->getTGroup());
        $group = $member->getTGroup();               

        $form = $this->createForm(new ExpenseType($group), $expense);
                     

        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) {          
        
    		$em = $this->getDoctrine()->getEntityManager();
    		$em->persist($expense);
    		$em->flush();

        	return $this->redirect($this->generateUrl('tk_expense_homepage'));
    	}}

    	return $this->render('TkExpenseBundle::new.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $expense = $em->getRepository('TkExpenseBundle:Expense')->find($id);
        $group = $expense->getGroup();
        
        $form = $this->createForm(new ExpenseType($group), $expense);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) { 

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($expense);
            $em->flush();

            return $this->redirect($this->generateUrl('tk_expense_homepage'));
        }}

        return $this->render('TkExpenseBundle::edit.html.twig', array(
            'id'                  => $id,
            'form'                => $form->createView(),
            'total_paid_by_me'    => $this->getTotalPaidByMeAction(),
            'total_paid_supposed' => $this->getTotalSupposedPaidAction(),
            'debts'               => $this->getCurrentDebtsAction(),
        ));
    }

    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $expense = $em->getRepository('TkExpenseBundle:Expense')->find($id);
        
        $em->remove($expense);
        $em->flush();

        return $this->indexAction();
    }

    private function getAllExpensesAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getAllExpenses($member);
    }

    private function getMyExpensesAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getMyExpenses($member);
    }

    private function getOtherExpensesAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getOtherExpenses($member);
    }

    private function getTotalPaidAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getTotalPaid($member->getTGroup());
    }

    private function getTotalPaidByMeAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getTotalPaidByMe($member);
    }

    private function getTotalSupposedPaidAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getTotalSupposedPaid($member);
    }

    private function getTotalPaidForMeAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getTotalPaidForMe($member);
    }

    private function getCurrentDebtsAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $expenses_service->getCurrentDebts($member->getTGroup());
    }
}
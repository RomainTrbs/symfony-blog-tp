<?php

namespace App\Controller;

use TCPDF;
use DateTimeImmutable;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Services\ImageUploaderHelper;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {
        $pdf = new TCPDF();

        $pdf->SetAuthor('SIO1 team');
        $pdf->SetTitle($formation->getName());

        $pdf->SetCellPaddings(1, 1, 1, 1);
        $pdf->SetCellMargins(1, 1, 1, 1);

        $pdf->AddPage();

        $pdf->setXY(10,10);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('helvetica','',18);
        $pdf->SetTextColor(0,0,255);
        $pdf->MultiCell(185,10,$formation->getName(),1,'P',1,0,'','',true);
        
        $pdf->setXY(10,25);
        $pdf->SetFillColor(10, 10, 10);
        $pdf->SetFont('times','',12);
        $pdf->SetTextColor(0,0,255);
        $pdf->writeHTML($formation->getContent());

        $pdf->Image('images/fcpro3.jpg', 10, 10, 55, 50);

        return $pdf->Output('fcpro-formation-'.$formation->getId().'.pdf', 'I') ;
    }

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAllInTheFutur(),
        ]);
    }

    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = new Formation();
        $formation->setCreatedBy($this->getUser());
        $formation->setCreatedAt(new DateTimeImmutable());

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ImageUploaderHelper $imageUploaderHelper, Formation $formation, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageUploaderHelper->uploadImage($form , $formation);

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}

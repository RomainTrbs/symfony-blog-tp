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
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/pdf/{id}', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
    {
        $pdf = new \TCPDF();

        $pdf->SetAuthor('SIO TEAM ! 💻');
        $pdf->SetTitle($formation->getName());
        $pdf->SetFont('times', '', 14);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetFillColor(160,222,255);
        $pdf->SetTextColor(0, 63,144);
        $pdf->Image('images/fcpro3.jpg', 10, 10, 37, 35, 'JPG', 'https://fcpro-rtirbois.bts.sio-ndlp.fr/page/1', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->MultiCell(187, 20, "PROGRAMME DE FORMATION", 0, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');

        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetFillColor(225,225,230);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(187, 10, $formation->getName(), 0, 'C', 1, 1, '', '', true);
        
        $pdf->setCellPaddings(3,3,3,3);
        $textg = '
        <style> .blue { color: rgb(0, 63,144); } .link { color: rgb(100,0,0); }</style>
        <br>
        <p class="blue">
<b>Tarifs :</b></p><p>
'. $formation->getPrice() .' € net.
        </p><br>
        <p class="blue">
<b>Modalités :</b>
        </p><p>
Formation individuelle<br>
2 journées de formation en présentiel<br>
14 heures (2x7 heures)
        </p><br>
        <p class="blue">
<b>Accessibilité aux personnes handicapées :</b>
</p><p>
<b>Accès au lieu de formation</b> :<br>
Les locaux sont accessibles aux
personnes en situation de handicap,
merci de nous contacter.<br>
<br>
<b>Accès à la prestation</b> :<br>
Une adaptation de la formation est
possible pour les personnes en
situation de handicap, merci de nous
contacter.
        </p><br>
        <p class="blue">
<b>Contact :</b>
        </p><p>
<b>Alexia HEBERT, responsable de FCPRO</b><br>
Service de Formation Professionnelle<br>
Continue de l’OGEC Notre Dame de la Providence<br>
<br>
9, rue chanoine Bérenger BP 340, 50300 AVRANCHES.<br>
Tel 02 33 58 02 22<br>
mail : <span class="link">fcpro@ndlpavranches.fr</span><br>
<br>
N° activité 25500040250<br>
OF certifié QUALIOPI pour les actions de formations<br>
<br>
Site Web : <span class="link">https://ndlpavranches.fr/fc-pro/</span>
        </p>';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(225,225,230);
        $pdf->writeHTMLCell(65, 230, "", "", $textg, 0, 0, 1, true, '', true);

        $textd = '
        <style>hr { color: rgb(0, 63,144); }</style>
        <p><b>Objectif de la formation</b>
        <hr>
        <ul><li>Objectif...</li><li>Objectif...</li><li>Objectif...</li></ul>
        <b>Prérequis necessaire / public visé</b>
        <hr>
        <ul><li>Prérequis...</li><li>Prérequis...</li></ul>
        <b>Modalités d\'accès et d\'inscription</b>
        <hr><br>
        <div>
<u>Dates</u> : '. $formation->getStartDateTime()->format('d/m/Y') .' à '. $formation->getEndDateTime()->format('d/m/Y') .'<br>
<u>Lieu</u> : ..
<br><br>
Nombre de stagiaires minimal : 0 – Nombre de stagiaires maximal : '. $formation->getCapacity() .'<br>
<i>Si le minimum requis de participants n’est pas atteint la session de formation
ne pourra avoir lieu.</i>
<br><br>

<b>Le chef d’établissement doit inscrire ses personnels auprès de FC PRO
(contact par mail ou par téléphone) au plus tard 7 jours avant le début de
la formation et faire la demande de prise en charge (sur OPCABOX pour
le personnel OGEC, auprès de FORMIRIS pour le personnel enseignant)
au plus tard 15 jours avant la date de début de la formation. L’inscription
des personnels enseignants sur FORMIRIS devra se faire 7 jours avant
la date de début de formation.</b></div><br>
<b>Moyens pédagogiques et techniques</b>
        <hr><br>
        <div>Supports visuels (power-point), apports théoriques et mises en situation.</div><br>
        <b>Modalité d\'évaluation</b>
        <hr><br>
        <div>Questionnaire de positionnement en début de formation + recueil des attentes
des participants.
Questionnaire d’évaluation des connaissances acquises en fin de formation.
Evaluation de satisfaction de la formation par les stagiaires.</div>
        </p>';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->writeHTMLCell(120, 230, "", "", $textd, 0, 0, 1, true, '', true);

        return $pdf->Output('fcpro-formation-' . $formation->getId() . '.pdf','I');
    }

    #[Route('/{id}/duplicate', name: 'app_formation_duplicate', methods: ['GET', 'POST'])]
    public function duplicate(Request $request, FormationRepository $formationRepository, TranslatorInterface $translator, Formation $formation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $formation2 = new Formation();
        $formation2->setCreatedAt($formation->getCreatedAt());
        $formation2->setCreatedBy($formation->getCreatedBy());
        $formation2->setContent($formation->getContent());

        $formation2->setCapacity($formation->getCapacity());
        $formation2->setStartDateTime($formation->getStartDateTime());
        $formation2->setEndDateTime($formation->getEndDateTime());
        $formation2->setImageFileName($formation->getImageFileName());
        $formation2->setName($formation->getName());
        $formation2->setPrice($formation->getPrice());

        $formationRepository->save($formation2, true);
        $this->addFlash('success', $translator->trans('The formation is copied'));

        return $this->redirectToRoute('app_formation_index');
    }

    /* #[Route('/futur', name: 'app_formation_futur', methods: ['GET'])]
    public function futur(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/futur.html.twig', [
            'formations' => $formationRepository->findAllInTheFutur(),
        ]);
    }
    */

    #[Route('/futur', name: 'app_formation_futur', methods: ['GET'])]
    public function futur(FormationRepository $formationRepository): Response
    {
        $formationsPerThree = array();

        $formations = $formationRepository->findAllInTheFutur();

        $i=0; $j=1;
        foreach ($formations as $formation) {
            $i++;
            if ($i>3) {
                $j++; $i=1;
            }
            $formationsPerThree[$j][$i] = $formation;
        }
        dump($formations);
        dump($formationsPerThree);
        
        return $this->render('formation/futur.html.twig', ['formations' => $formationsPerThree,]);
    }

    #[Route('/catalog', name: 'app_formation_catalog', methods: ['GET'])]
    public function catalog(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/catalog.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImageUploaderHelper $imageUploaderHelper, FormationRepository $formationRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $formation = new Formation();
        $formation->setCreatedAt(new DateTimeImmutable());
        $formation->setCreatedBy($this->getUser());

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploaderHelper->uploadImage($form, $formation);
            if (!empty($errorMessage)) {
                $this->addFlash ('danger', $translator->trans('An error has occured: ') . $errorMessage);
            }
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
    public function edit(Request $request, ImageUploaderHelper $imageUploaderHelper, FormationRepository $formationRepository, Formation $formation, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploaderHelper->uploadImage($form, $formation);
            if (!empty($errorMessage)) {
                $this->addFlash ('danger', $translator->trans('An error has occured: ') . $errorMessage);
            }
            $formationRepository->save($formation, true);

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
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) 
        {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
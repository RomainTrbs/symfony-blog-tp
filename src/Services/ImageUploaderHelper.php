<?php

namespace App\Services;

use App\Repository\FormationRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploaderHelper {

    public function __construct(SluggerInterface $slugger, FormationRepository $formationRepository ,TranslatorInterface $translator){
        $this->slugger = $slugger ;
        $this->translator = $translator;
    }
    
    public function uploadImage($form) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger','an error occured', $e->getMessage());
            }

            $formation->setImageFilename($newFilename);
        }


        $formationRepository->save($formation, true);
    }

}
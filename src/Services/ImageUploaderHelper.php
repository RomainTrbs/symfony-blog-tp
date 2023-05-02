<?php

namespace App\Services;

use App\Repository\FormationRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploaderHelper {

    private $slugger;
    private $params;
    private $flash;

    public function __construct(ParameterBagInterface $params, SluggerInterface $slugger, FormationRepository $formationRepository ,TranslatorInterface $translator){
        $this->slugger = $slugger ;
        $this->translator = $translator;
        $this->params = $params ;
    }
    
    public function uploadImage($form, $formation): String
     {
        $errorMessage = "";
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->params->get('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $errorMessage = $e->getMessage();
            }

            $formation->setImageFilename($newFilename);
        }


        return $errorMessage;
    }

}
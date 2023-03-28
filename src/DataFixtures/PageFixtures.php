<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Contracts\Translation\TranslatorInterface;


class PageFixtures extends Fixture
{
    private $translator;

    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }
    
    public function load(ObjectManager $manager): void
    {
        $home = new Page();
        $home->setTitle($this->translator->trans("Home"));
        $home->setText("<h1>" . $this->translator->trans("Home") . "</h1>");
        $manager->persist($home);

        $centre = new Page();
        $centre->setTitle($this->translator->trans("Formation Center"));
        $centre->setText("<h1>" . $this->translator->trans("Formation Center") . "</h1>");
        $manager->persist($centre);

        $catalogue = new Page();
        $catalogue->setTitle($this->translator->trans("Catalogue"));
        $catalogue->setText("<h1>" . $this->translator->trans("Catalogue") . "</h1>");
        $manager->persist($catalogue);

        $rate = new Page();
        $rate->setTitle($this->translator->trans("Rate a Formation"));
        $rate->setText("<h1>" . $this->translator->trans("Rate a Formation") . "</h1>");

        $manager->persist($rate);

        $tuto = new Page();
        $tuto->setTitle($this->translator->trans("Tutos"));
        $tuto->setText("<h1>" . $this->translator->trans("Tutos") . "</h1>");
        $manager->persist($tuto);

        $certif = new Page();
        $certif->setTitle($this->translator->trans("Qualiopi Certification"));
        $certif->setText("<h1>" . $this->translator->trans("Qualiopi Certification") . "</h1>");
        $manager->persist($certif);

        $contact = new Page();
        $contact->setTitle($this->translator->trans("Contact"));
        $contact->setText("<h1>" . $this->translator->trans("Contact") . "</h1>");
        $manager->persist($contact);

        $infos = new Page();
        $infos->setTitle($this->translator->trans("Infos légales"));
        $infos->setText("<h1>" . $this->translator->trans("Infos légales") . "</h1>");
        $manager->persist($infos);

        $regles = new Page();
        $regles->setTitle($this->translator->trans("Règles de confidentialité"));
        $regles->setText("<h1>" . $this->translator->trans("Règles de confidentialité") . "</h1>");
        $manager->persist($regles);

        $reserve = new Page();
        $reserve->setTitle($this->translator->trans("Réservé"));
        $reserve->setText("<h1>" . $this->translator->trans("Réservé") . "</h1>");
        $manager->persist($reserve);

        $manager->flush();
    }
}

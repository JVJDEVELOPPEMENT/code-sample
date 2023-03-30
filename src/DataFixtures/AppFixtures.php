<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\LabelFactory;
use App\Factory\OddFactory;
use App\Factory\ProductTypeFactory;
use App\Factory\SectorFactory;
use App\Factory\StructureTypeFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadDefaultsData();
    }

    private function loadDefaultsData(): void
    {
        foreach ($this->getCategories() as $name) {
            CategoryFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getOdds() as $name) {
            OddFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getTags() as $name) {
            TagFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getSectorNames() as $name) {
            SectorFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getStructureTypesName() as $name) {
            StructureTypeFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getLabelsName() as $name) {
            LabelFactory::createOne([
                'name' => $name,
            ]);
        }

        foreach ($this->getProductTypes() as $productType) {
            ProductTypeFactory::createOne([
                'name' => $productType['name'],
                'label' => $productType['label'],
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function getCategories(): array
    {
        return [
            'Agroalimentaire & Agriculture',
            'BTP & Construction',
            'Communication & Événementiel',
            'Déchets & Recyclage',
            'Education',
            'Energie',
            'Environnement & Biodiversité',
            'Finance & Assurance',
            'Informatique & Électronique',
            'Logistique, Transport & Mobilité',
            'Mobilier & Aménagement',
            'Réseau et infrastructure numérique',
            'Ressources & Matériaux',
            'Restauration & Hébergement',
            'Retail & Distribution',
            'RH & QHSE',
            'Textile',
        ];
    }

    /**
     * @return string[]
     */
    private function getOdds(): array
    {
        return [
            'Objectif 1 : Pas de pauvreté',
            'Objectif 2 : Faim “zéro”',
            'Objectif 3 : Bonne santé et bien être',
            'Objectif 4 : Éducation de qualité',
            'Objectif 5 : Egalité entre les sexes',
            'Objectif 6 : Eau propre et assainissement',
            'Objectif 7 : Énergie propre et d’un coût abordable',
            'Objectif 8 : Travail décent et croissance économique',
            'Objectif 9 : Industrie, innovation et infrastructure',
            'Objectif 10 : Inégalités réduites',
            'Objectif 11 : Villes et communautés durables',
            'Objectif 12 : Consommation et production durables',
            'Objectif 13 : Mesures relatives à la lutte contre les changements climatiques',
            'Objectif 14 : Vie aquatique',
            'Objectif 15 : Vie terrestre',
            'Objectif 16 : Paix, justice et institutions efficaces',
            'Objectif 17 : Partenariats pour la réalisation des objectifs',
        ];
    }

    /**
     * @return string[]
     */
    private function getTags(): array
    {
        return ['Formation', 'Conseil', 'Diagnostic', 'Financement', 'Recrutement'];
    }

    /**
     * @return string[]
     */
    private function getSectorNames(): array
    {
        return [
            'Administration & Fonction publique',
            'Agriculutre & Agroalimentaire',
            'Banque, Finance, Assurance',
            'BTP & Construction',
            'Bois, Papier, Carton, Imprimerie',
            'Chimie, Pharmacie, Santé',
            'Commerce, Négoce, Distribution',
            'Culture, Sport & Loisirs',
            'Enseignement & Formation',
            'Environnment & Énergie',
            'Hébergement, Restauration, Tourisme',
            'Industrie automobile et autres matériels de transport',
            'Industries extractives',
            'Industrie manufacturière',
            'Informatique & Électronique',
            'Information, Communication & Événementiel',
            'Logistique & Transport',
            'Métalurgie',
            'Numérique & Télécom',
            'Services & Conseil',
            'Textile & Habillement',
        ];
    }

    /**
     * @return string[]
     */
    private function getStructureTypesName(): array
    {
        return [
            'Entreprise',
            'Collectivité Territoriale',
            'Établissement public',
            'Fédération',
            'Association',
            'Think tank',
            'Freelance',
        ];
    }

    /**
     * @return string[]
     */
    private function getLabelsName(): array
    {
        return [
            '1% for the planet',
            '80 Plus',
            'A+',
            'Ad for good',
            'Agri Ethique France',
            'Agriculture Biologique',
            'Altereco',
            'AOC',
            'AOP',
            'BCorp',
            'Bio Equitable en France',
            'BioED',
            'Bluesign',
            'Capenergies',
            'Climate Neutral Now',
            'Cloud de confiance',
            'Cradle to cradle',
            'Eco Emballage',
            'Ecocert',
            'Ecolabel européen',
            'EcoVadis',
            'Eko',
            'Energy Star',
            'Engagé RSE',
            'Entreprise du patrimoine vivant',
            'EnVol',
            'Epeat',
            'ESS ADN',
            'Fair for life',
            'France Terre Textile',
            'FSC',
            'Gots',
            'Green Code Label',
            'Greenfin Label',
            'Greenguard',
            'Greenspector',
            'HQE',
            'IEEE',
            'IGP',
            "Imprim'Vert",
            'ISO 26000',
            'ISO 9001',
            'Label bas carbone',
            'Label Biodiversity Progress',
            'Label Initiative Remarquable',
            'Label ISR',
            'Label Numérique Responsable',
            'Label Relations fournisseurs et achats responsables (RFAR)',
            'Label Toumaï',
            'Label Tourisme Equitable',
            'LEED',
            'Lucie',
            'Max Havelaar Fair Trade',
            'More',
            'MSC Pèche durable',
            'Nature & Progrès',
            'Nature Plus',
            'NF Environnement',
            'Numérique Responsable',
            'Origine France Garantie',
            'PEFC',
            'PME+',
            'Positive Workplace',
            'Print Ethic',
            'Produit en Bretagne',
            'Qualiopi',
            'Rainforest Alliance',
            'Solar impulse Efficient Solution Label',
            'STG',
            'TCO Certified',
            'The Green web foundation',
            'UTZ Certified',
            'VertVolt',
            'VOC FREE',
            'Wattimpact',
            'WTFO',
            'WWF',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getProductTypes(): array
    {
        return [
            [
                'name' => 'shelf',
                'label' => 'Sur étagère',
            ],
            [
                'name' => 'tailored',
                'label' => 'Sur mesure',
            ],
            [
                'name' => 'advice',
                'label' => 'Conseil',
            ],
            [
                'name' => 'funding',
                'label' => 'Financement',
            ],
            [
                'name' => 'training',
                'label' => 'Formation',
            ],
        ];
    }
}

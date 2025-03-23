<?php

namespace App\Controller\Admin;

use App\Entity\ImgProducts;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;


class ImgProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImgProducts::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            //TextField::new('Name', 'Nom'),

            ImageField::new('Nomimage') // Assurez-vous que 'image' correspond au nom de la propriété dans votre entité ImgProducts
                ->setBasePath('/images/products') // Chemin vers le dossier contenant les images uploadées
                ->setUploadDir('public/images/products') // Chemin du répertoire de téléchargement relatif au dossier public
                ->setLabel('Image'),
             

        ];
    }
    
}

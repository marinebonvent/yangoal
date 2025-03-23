<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ImgProductsType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('title', 'Nom du produit');
        yield NumberField::new('price', 'Prix actuel')
        ->setNumDecimals(2) // Nombre de décimales
        
        ->setFormTypeOption('input', 'number') // Assure que le champ est traité comme un nombre
        ->formatValue(fn ($value) => number_format($value, 2, ',', '.')); // Affiche 3.900,20
            yield NumberField::new('percentage', 'Pourcentage');
            yield AssociationField::new('category', 'Catégorie');
        yield TextEditorField::new('summary', 'Détails');

        yield TextEditorField::new('description_0', 'Description');
        yield TextEditorField::new('description_1', 'Seconde description');
        yield TextEditorField::new('description_2', 'Troisième description');

        yield CollectionField::new('imgProducts', 'Images')
            ->setEntryType(ImgProductsType::class) // Utiliser un FormType dédié pour ImgProducts
            ->setFormTypeOptions([
                'by_reference' => false, // Important pour que addImgProduct soit appelé
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
            ])
            ->onlyOnForms();
    }
}
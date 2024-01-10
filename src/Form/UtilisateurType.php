<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UtilisateurType extends AbstractType
{
    public function __construct(AuthorizationCheckerInterface $auth) {
        $this->auth = $auth;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email', EmailType::class,[])
            ->add('password')
            // ->add('adresse')
            // ->add('cp')
            // ->add('ville')
            // ->add('creer_le')
            // ->add('supprimer_le')
        ;
        //Si admin
        if ($this->auth->isGranted('ROLE_ADMIN')) {
            $builder ->add('roles', CollectionType::class, [ ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

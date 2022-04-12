<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class FrToDatetimeTransformer implements DataTransformerInterface {

    // Transforme les données originelles pour qu'elle puissent s'afficher dans un formulaire
    public function transform($date){

        if($date === null){
            return '';
        }

        // retourne une date en fr
        return $date->format('d/m/Y');
    }

    // Inverse de transform prend la donnée qui arrive et la remet dans le format qu'on attend
    public function reverseTransform($dateFr){

        if($dateFr === null){
            // exception

            throw new TransformationFailedException("fournir une date");
        }

        $date = \DateTime::createFromFormat('d/m/Y',$dateFr);

        if($date === false){
            // exception

            throw new TransformationFailedException("Le format de la date n'est pas correct");
        }

        return $date;
    }

}